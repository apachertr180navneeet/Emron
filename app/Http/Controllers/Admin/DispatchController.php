<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DispatchOrder;
use App\Models\DispatchOrderItem;
use App\Models\Customer;
use App\Models\Item;
use Exception;

class DispatchController extends Controller
{
    protected function getCompanyId()
    {
        return Auth::user()->company_id;
    }

    public function index(Request $request)
    {
        try {
            $query = DispatchOrder::with('customer');
            if ($companyId = $this->getCompanyId()) {
                $query->where('company_id', $companyId);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('challan_no', 'like', "%{$s}%")
                      ->orWhere('transport_name', 'like', "%{$s}%")
                      ->orWhereHas('customer', function ($qv) use ($s) {
                          $qv->where('customer_name', 'like', "%{$s}%");
                      });
                });
            }

            if ($request->filled('status')) {
                $query->where('dispatch_status', $request->status);
            }

            $dispatchOrders = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.dispatch._table', compact('dispatchOrders'))->render(),
                    'pagination' => view('admin.dispatch._pagination', compact('dispatchOrders'))->render(),
                ]);
            }

            return view('admin.dispatch.index', compact('dispatchOrders'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $customers = Customer::forCompany($companyId)->where('status', 'active')->orderBy('customer_name')->get();
        $items = Item::forCompany()->where('status', 'active')->orderBy('item_name')->get();
        $lastChallan = DispatchOrder::withTrashed()->where('company_id', $companyId)->latest('id')->value('challan_no');
        $nextChallan = $lastChallan ? 'CH-' . str_pad(((int) substr($lastChallan, 3)) + 1, 5, '0', STR_PAD_LEFT) : 'CH-00001';

        return view('admin.dispatch.create', compact('customers', 'items', 'nextChallan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dispatch_date'      => 'required|date',
            'challan_no'         => 'required|max:255|unique:dispatch_orders,challan_no',
            'customer_id'        => 'required|exists:customers,id',
            'customer_mobile'    => 'required',
            'transport_name'     => 'required|max:255',
            'dispatch_status'    => 'required|in:Pending,In Transit,Delivered,Cancelled',
            'total_amount'       => 'required|numeric|min:0',
            'items'              => 'required|array|min:1',
            'items.*.lot_no'     => 'required|max:255',
            'items.*.item_id'    => 'nullable|exists:items,id',
            'items.*.qty'        => 'required|numeric|min:0.01',
            'items.*.weight'     => 'required|numeric|min:0.01',
            'items.*.rate'       => 'required|numeric|min:0',
            'items.*.amount'     => 'required|numeric|min:0',
        ]);

        try {
            $dispatchOrder = DispatchOrder::create([
                'company_id'      => $this->getCompanyId(),
                'dispatch_date'   => $request->dispatch_date,
                'challan_no'      => $request->challan_no,
                'customer_id'     => $request->customer_id,
                'customer_mobile' => $request->customer_mobile,
                'transport_name'  => $request->transport_name,
                'dispatch_status' => $request->dispatch_status,
                'total_amount'    => $request->total_amount,
                'status'          => 'active',
                'created_by'      => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                $dispatchOrder->items()->create([
                    'lot_no'  => $item['lot_no'],
                    'item_id' => $item['item_id'] ?? null,
                    'qty'     => $item['qty'],
                    'weight'  => $item['weight'],
                    'rate'    => $item['rate'],
                    'amount'  => $item['amount'],
                ]);
            }

            return redirect()->route('admin.dispatch.index')->with('success', 'Dispatch order created successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(DispatchOrder $dispatchOrder)
    {
        $this->authorizeAccess($dispatchOrder);
        $dispatchOrder->load('items');

        $companyId = $this->getCompanyId() ?? $dispatchOrder->company_id;
        $customers = Customer::forCompany($companyId)->where('status', 'active')->orderBy('customer_name')->get();
        $items = Item::forCompany()->where('status', 'active')->orderBy('item_name')->get();

        return view('admin.dispatch.edit', compact('dispatchOrder', 'customers', 'items'));
    }

    public function update(Request $request, DispatchOrder $dispatchOrder)
    {
        $this->authorizeAccess($dispatchOrder);

        $request->validate([
            'dispatch_date'      => 'required|date',
            'challan_no'         => 'required|max:255|unique:dispatch_orders,challan_no,' . $dispatchOrder->id,
            'customer_id'        => 'required|exists:customers,id',
            'customer_mobile'    => 'required',
            'transport_name'     => 'required|max:255',
            'dispatch_status'    => 'required|in:Pending,In Transit,Delivered,Cancelled',
            'total_amount'       => 'required|numeric|min:0',
            'items'              => 'required|array|min:1',
            'items.*.lot_no'     => 'required|max:255',
            'items.*.item_id'    => 'nullable|exists:items,id',
            'items.*.qty'        => 'required|numeric|min:0.01',
            'items.*.weight'     => 'required|numeric|min:0.01',
            'items.*.rate'       => 'required|numeric|min:0',
            'items.*.amount'     => 'required|numeric|min:0',
        ]);

        try {
            $dispatchOrder->update([
                'dispatch_date'   => $request->dispatch_date,
                'challan_no'      => $request->challan_no,
                'customer_id'     => $request->customer_id,
                'customer_mobile' => $request->customer_mobile,
                'transport_name'  => $request->transport_name,
                'dispatch_status' => $request->dispatch_status,
                'total_amount'    => $request->total_amount,
            ]);

            $dispatchOrder->items()->delete();

            foreach ($request->items as $item) {
                $dispatchOrder->items()->create([
                    'lot_no'  => $item['lot_no'],
                    'item_id' => $item['item_id'] ?? null,
                    'qty'     => $item['qty'],
                    'weight'  => $item['weight'],
                    'rate'    => $item['rate'],
                    'amount'  => $item['amount'],
                ]);
            }

            return redirect()->route('admin.dispatch.index')->with('success', 'Dispatch order updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(DispatchOrder $dispatchOrder)
    {
        $this->authorizeAccess($dispatchOrder);
        try {
            $dispatchOrder->items()->delete();
            $dispatchOrder->delete();
            return redirect()->route('admin.dispatch.index')->with('success', 'Dispatch order deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reports(Request $request)
    {
        $companyId = $this->getCompanyId();

        $query = DispatchOrder::with('customer', 'items');
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('dispatch_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('dispatch_date', '<=', $request->to_date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('dispatch_status', $request->status);
        }

        // Customer filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Transport filter
        if ($request->filled('transport_name')) {
            $query->where('transport_name', 'like', "%{$request->transport_name}%");
        }

        $reportType = $request->report_type ?? 'summary';
        $dispatchOrders = $query->latest()->get();
        $customers = Customer::forCompany($companyId)->where('status', 'active')->orderBy('customer_name')->get();

        // Aggregate data for different reports
        $customerWise = $dispatchOrders->groupBy('customer_id')->map(function ($orders, $cid) {
            $customer = $orders->first()->customer;
            return [
                'customer_name' => $customer->customer_name ?? 'Unknown',
                'total_orders'  => $orders->count(),
                'total_amount'  => $orders->sum('total_amount'),
            ];
        });

        $transportWise = $dispatchOrders->groupBy('transport_name')->map(function ($orders, $transport) {
            return [
                'transport_name' => $transport,
                'total_orders'   => $orders->count(),
                'total_amount'   => $orders->sum('total_amount'),
            ];
        });

        $statusWise = $dispatchOrders->groupBy('dispatch_status')->map(function ($orders, $status) {
            return [
                'status'       => $status,
                'total_orders' => $orders->count(),
                'total_amount' => $orders->sum('total_amount'),
            ];
        });

        return view('admin.dispatch.reports', compact(
            'dispatchOrders', 'customers', 'reportType',
            'customerWise', 'transportWise', 'statusWise'
        ));
    }

    protected function authorizeAccess(DispatchOrder $dispatchOrder)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $dispatchOrder->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
