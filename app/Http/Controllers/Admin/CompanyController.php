<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $company = Company::latest('id')->first();

        return view('admin.company.index', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('admin.company.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name'                => 'required',
            'postal_code'         => 'required|digits:7',
            'address'             => 'required',
            'representative'      => 'required',
            'establishment_date'  => 'required',
            'capital'             => 'required',
            'business'            => 'required',
            'number_of_employees' => 'required',
        ]);

        $company->name                  = $request->input('name');
        $company->postal_code           = $request->input('postal_code');
        $company->address               = $request->input('address');
        $company->representative        = $request->input('representative');
        $company->establishment_date    = $request->input('establishment_date');
        $company->capital               = $request->input('capital');
        $company->business              = $request->input('business');
        $company->number_of_employees   = $request->input('number_of_employees');
        $company->save();

        return redirect()->route('admin.company.index')->with('flash_message', '会社概要を登録しました。');
    }

}
