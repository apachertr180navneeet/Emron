<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\Unit;
use Exception;

class PurchaseController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $query = Purchase::with('vendor');
            if ($companyId = $this->getCompanyId()) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('challan_no', 'like', "%{$s}%")
                      ->orWhere('bno', 'like', "%{$s}%")
                      ->orWhereHas('vendor', function ($qv) use ($s) {
                          $qv->where('vendor_name', 'like', "%{$s}%")
                             ->orWhere('firm_name', 'like', "%{$s}%");
                      });
                });
            }

            $purchases = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.purchase._table', compact('purchases'))->render(),
                    'pagination' => view('admin.purchase._pagination', compact('purchases'))->render(),
                ]);
            }

            return view('admin.purchase.index', compact('purchases'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $vendors = Vendor::forCompany($companyId)->where('status', 'active')->orderBy('vendor_name')->get();
        $items = Item::forCompany()->where('item_type', 'Raw Material')->where('status', 'active')->orderBy('item_name')->get();
        $units = Unit::forCompany()->where('status', 'active')->orderBy('unit_name')->get();

        return view('admin.purchase.create', compact('vendors', 'items', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id'          => 'required|exists:vendors,id',
            'purchase_date'      => 'required|date',
            'bno'                => 'required|max:255',
            'challan_no'         => 'required|max:255',
            'transport'          => 'nullable|max:255',
            'lr_no'              => 'nullable|max:255',
            'subtotal'           => 'required|numeric|min:0',
            'gst_percentage'     => 'required|numeric|min:0|max:100',
            'tax_amount'         => 'required|numeric|min:0',
            'discount'           => 'required|numeric|min:0',
            'transport_charges'  => 'required|numeric|min:0',
            'other_charges'      => 'required|numeric|min:0',
            'total_amount'       => 'required|numeric|min:0',
            'purchase_status'    => 'required|in:Pending,Completed,Cancelled',
            'notes'              => 'nullable|max:1000',
            'items'              => 'required|array|min:1',
            'items.*.item_id'    => 'nullable|exists:items,id',
            'items.*.item_name'  => 'required|max:255',
            'items.*.unit'       => 'required|max:50',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.rate'       => 'required|numeric|min:0',
            'items.*.amount'     => 'required|numeric|min:0',
        ]);

        try {
            $subtotal = $request->subtotal;
            $gst = $request->gst_percentage;
            $taxAmount = $subtotal * ($gst / 100);
            $discount = $request->discount;
            $transportCharges = $request->transport_charges;
            $otherCharges = $request->other_charges;
            $totalAmount = $subtotal + $taxAmount - $discount + $transportCharges + $otherCharges;

            $purchase = Purchase::create([
                'company_id'        => $this->getCompanyId(),
                'vendor_id'         => $request->vendor_id,
                'purchase_date'     => $request->purchase_date,
                'invoice_no'        => $request->challan_no,
                'bno'               => $request->bno,
                'challan_no'        => $request->challan_no,
                'transport'         => $request->transport,
                'lr_no'             => $request->lr_no,
                'subtotal'          => $subtotal,
                'gst_percentage'    => $gst,
                'tax_amount'        => round($taxAmount, 2),
                'discount'          => $discount,
                'transport_charges' => $transportCharges,
                'other_charges'     => $otherCharges,
                'total_amount'      => round($totalAmount, 2),
                'purchase_status'   => $request->purchase_status,
                'notes'             => $request->notes,
                'status'            => 'active',
                'created_by'        => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'item_id'    => $item['item_id'] ?? null,
                    'item_name'  => $item['item_name'],
                    'unit'       => $item['unit'],
                    'quantity'   => $item['quantity'],
                    'rate'       => $item['rate'],
                    'amount'     => $item['amount'],
                    'created_by' => Auth::id(),
                ]);
            }

            return redirect()->route('admin.purchase.index')->with('success', 'Purchase created successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Purchase $purchase)
    {
        $this->authorizeAccess($purchase);
        $purchase->load('items');

        $companyId = $this->getCompanyId() ?? $purchase->company_id;
        $vendors = Vendor::forCompany($companyId)->where('status', 'active')->orderBy('vendor_name')->get();
        $items = Item::forCompany()->where('item_type', 'Raw Material')->where('status', 'active')->orderBy('item_name')->get();
        $units = Unit::forCompany()->where('status', 'active')->orderBy('unit_name')->get();

        return view('admin.purchase.edit', compact('purchase', 'vendors', 'items', 'units'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $this->authorizeAccess($purchase);

        $request->validate([
            'vendor_id'          => 'required|exists:vendors,id',
            'purchase_date'      => 'required|date',
            'bno'                => 'required|max:255',
            'challan_no'         => 'required|max:255',
            'transport'          => 'nullable|max:255',
            'lr_no'              => 'nullable|max:255',
            'subtotal'           => 'required|numeric|min:0',
            'gst_percentage'     => 'required|numeric|min:0|max:100',
            'tax_amount'         => 'required|numeric|min:0',
            'discount'           => 'required|numeric|min:0',
            'transport_charges'  => 'required|numeric|min:0',
            'other_charges'      => 'required|numeric|min:0',
            'total_amount'       => 'required|numeric|min:0',
            'purchase_status'    => 'required|in:Pending,Completed,Cancelled',
            'notes'              => 'nullable|max:1000',
            'items'              => 'required|array|min:1',
            'items.*.item_id'    => 'nullable|exists:items,id',
            'items.*.item_name'  => 'required|max:255',
            'items.*.unit'       => 'required|max:50',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.rate'       => 'required|numeric|min:0',
            'items.*.amount'     => 'required|numeric|min:0',
        ]);

        try {
            $subtotal = $request->subtotal;
            $gst = $request->gst_percentage;
            $taxAmount = $subtotal * ($gst / 100);
            $discount = $request->discount;
            $transportCharges = $request->transport_charges;
            $otherCharges = $request->other_charges;
            $totalAmount = $subtotal + $taxAmount - $discount + $transportCharges + $otherCharges;

            $purchase->update([
                'vendor_id'         => $request->vendor_id,
                'purchase_date'     => $request->purchase_date,
                'invoice_no'        => $request->challan_no,
                'bno'               => $request->bno,
                'challan_no'        => $request->challan_no,
                'transport'         => $request->transport,
                'lr_no'             => $request->lr_no,
                'subtotal'          => $subtotal,
                'gst_percentage'    => $gst,
                'tax_amount'        => round($taxAmount, 2),
                'discount'          => $discount,
                'transport_charges' => $transportCharges,
                'other_charges'     => $otherCharges,
                'total_amount'      => round($totalAmount, 2),
                'purchase_status'   => $request->purchase_status,
                'notes'             => $request->notes,
            ]);

            $purchase->items()->delete();

            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'item_id'    => $item['item_id'] ?? null,
                    'item_name'  => $item['item_name'],
                    'unit'       => $item['unit'],
                    'quantity'   => $item['quantity'],
                    'rate'       => $item['rate'],
                    'amount'     => $item['amount'],
                    'created_by' => Auth::id(),
                ]);
            }

            return redirect()->route('admin.purchase.index')->with('success', 'Purchase updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function toggleStatus(Purchase $purchase)
    {
        $this->authorizeAccess($purchase);
        try {
            $purchase->status = $purchase->status === 'active' ? 'inactive' : 'active';
            $purchase->save();
            return response()->json(['success' => true, 'status' => $purchase->status]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Purchase $purchase)
    {
        $this->authorizeAccess($purchase);
        try {
            $purchase->items()->delete();
            $purchase->delete();
            return redirect()->route('admin.purchase.index')->with('success', 'Purchase deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function authorizeAccess(Purchase $purchase)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $purchase->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
