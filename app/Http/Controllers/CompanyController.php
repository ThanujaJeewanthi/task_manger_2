<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        //get all companies
        $companies = Company::paginate(10);
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',


        ]);

        $data = $request->all();
        $data['has_clients'] = $request->has('has_clients');
        $data['active'] = $request->has('is_active');
        $data['created_by'] = Auth::id();


        Company::create($data);

        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
{
    return view('companies.show', compact('company'));
}

    public function edit(Company $company)
    {

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',


        ]);

        $data = $request->all();
        $data['has_clients'] = $request->has('has_clients');
        $data['active'] = $request->has('is_active');
        $data['updated_by'] = Auth::id();



        $company->update($data);

        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $company->update(['active' => false, 'updated_by' => Auth::id()]);
        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }
}
