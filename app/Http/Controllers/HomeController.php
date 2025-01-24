<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Restaurant;

class HomeController extends Controller
{
    public function index()
    {
        $highly_rated_restaurants = Restaurant::take(6)->get();
        $categories = Category::all();
        $new_restaurants = Restaurant::orderBy('created_at', 'desc')->take(6)->get();

        return view('home', compact('highly_rated_restaurants','categories','new_restaurants'));
    }
}
