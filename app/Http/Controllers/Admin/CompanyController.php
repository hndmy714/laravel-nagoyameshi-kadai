<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index() {
        $company = Company::first();

        return view('admin.company.index', compact('company'));
    }

    public function edit(Company $company) {
        return view('admin.company.edit', compact('company'));
    }

    public function update(Request $request, Company $company) {
        $request->validate([
            'name' => 'required',
            'postal_code' => 'required|digits:7',
            'address' => 'required',
            'representative' => 'required',
            'establishment_date' => 'required',
            'capital' => 'required',
            'business' => 'required',
            'number_of_employees' => 'required',
        ]);

        $company->name = $request->input('name');
        $c
}
