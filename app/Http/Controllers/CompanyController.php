<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    //indexアクション（会社概要ページ）
    public function index()
    {
        $company = Company::first();

        return view('company.index', compact('company'));
    }
}
