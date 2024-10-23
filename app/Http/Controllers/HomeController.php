<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $categories = Category::all();
        //$highly_rated_restaurants = Product::orderBy('created_at', 'desc')->take(6)->get();
        $highly_rated_restaurants = Restaurant::take(6)->get();
        $new_restaurants = Restaurant::orderBy('created_at', 'desc')->take(6)->get();

        return view('home', compact('highly_rated_restaurants','categories','new_restaurants'));
    }

}
