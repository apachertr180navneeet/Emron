<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Validator, File, Exception;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Company::query();

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('company_name', 'like', "%{$s}%")
                      ->orWhere('owner_name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                      ->orWhere('city', 'like', "%{$s}%")
                      ->orWhere('mobile', 'like', "%{$s}%");
                });
            }

            $perPage = $request->get('per_page', 10);
            $companies = $query->latest()->paginate($perPage);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.company._table', compact('companies'))->render(),
                    'pagination' => view('admin.company._pagination', compact('companies'))->render(),
                ]);
            }

            return view('admin.company.index', compact('companies'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.company.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'company_name' => 'required',
                'owner_name'   => 'required',
                'mobile'       => 'required|digits:10|unique:companies,mobile|unique:users,phone',
                'email'        => 'required|email|unique:companies,email|unique:users,email',
                'city'         => 'required',
                'logo'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                'password'     => 'nullable|min:6',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }

            $data = $request->except('logo');
            $data['status'] = 'active';

            if ($request->file('logo')) {
                $file = $request->file('logo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $folder = 'uploads/company/';
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }
                $file->move($path, $filename);
                $data['logo'] = $folder . $filename;
            }

            $data['created_by'] = Auth::id();
            $company = Company::create($data);

            User::create([
                'company_id' => $company->id,
                'first_name' => $request->owner_name,
                'last_name'  => $request->owner_name,
                'full_name'  => $request->owner_name,
                'username'   => $request->username,
                'slug'       => Str::slug($request->owner_name . '-' . uniqid()),
                'email'      => $request->email,
                'phone'      => $request->mobile,
                'password'   => $request->password ? Hash::make($request->password) : Hash::make('password'),
                'role'       => 'user',
                'avatar'     => $data['logo'] ?? '',
                'address'    => $request->address ?? '',
                'city'       => $request->city ?? '',
                'state'      => $request->state ?? '',
                'zipcode'    => $request->pin_code ?? '',
                'country'    => 'India',
            ]);

            return redirect()->route('admin.company.index')->with('success', 'Company created successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(Company $company)
    {
        return view('admin.company.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        try {
            $validator = Validator::make($request->all(), [
                'company_name' => 'required',
                'owner_name'   => 'required',
                'mobile'       => 'required|digits:10|unique:companies,mobile,' . $company->id . '|unique:users,phone,' . User::where('email', $company->email)->value('id'),
                'email'        => 'required|email|unique:companies,email,' . $company->id . '|unique:users,email,' . User::where('email', $company->email)->value('id'),
                'city'         => 'required',
                'logo'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                'password'     => 'nullable|min:6',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }

            $data = $request->except('logo');
            $data['status'] = $request->has('status') ? $request->status : $company->status;

            if ($request->file('logo')) {
                if ($company->logo && File::exists(public_path($company->logo))) {
                    File::delete(public_path($company->logo));
                }
                $file = $request->file('logo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $folder = 'uploads/company/';
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }
                $file->move($path, $filename);
                $data['logo'] = $folder . $filename;
            }

            $company->update($data);

            $user = User::where('email', $company->email)->first();
            if ($user) {
                $userData = [
                    'first_name' => $request->owner_name,
                    'last_name'  => $request->owner_name,
                    'full_name'  => $request->owner_name,
                    'username'   => $request->username,
                    'phone'      => $request->mobile,
                    'email'      => $request->email,
                    'avatar'     => $company->logo ?? '',
                    'address'    => $request->address ?? '',
                    'city'       => $request->city ?? '',
                    'state'      => $request->state ?? '',
                    'zipcode'    => $request->pin_code ?? '',
                ];
                if ($request->password) {
                    $userData['password'] = Hash::make($request->password);
                }
                $user->update($userData);
            }

            return redirect()->route('admin.company.index')->with('success', 'Company updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(Company $company)
    {
        try {
            $company->status = $company->status === 'active' ? 'inactive' : 'active';
            $company->save();
            User::where('company_id', $company->id)->update(['status' => $company->status]);
            return response()->json(['success' => true, 'status' => $company->status]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
