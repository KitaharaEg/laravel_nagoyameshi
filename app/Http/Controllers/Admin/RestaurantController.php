<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;
use APP\Models\RegularHoliday;

class RestaurantController extends Controller
{
/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 検索ボックスに入力されたキーワードを取得する
        $keyword = $request->input('keyword');

        // キーワードが存在すれば検索を行い、そうでなければ全件取得する
        if ($keyword) {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")->orWhere('name', 'like', "%{$keyword}%")->paginate(15);
        } else {
            $restaurants = Restaurant::paginate(15);
        }

        $total = $restaurants->total();
        return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $regular_holidays = RegularHoliday::all();

        return view('admin.restaurants.create', compact('categories','regular_holidays'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required',
            'image'             => 'image|max:2048',
            'description'       => 'required',
            'lowest_price'      => 'required|numeric|min:0|lte:highest_price',
            'highest_price'     => 'required|numeric|min:0|gte:lowest_price',
            'postal_code'       => 'required|digits:7',
            'address'           => 'required',
            'opening_time'      => 'required|before:closing_time',
            'closing_time'      => 'required|after:opening_time',
            'seating_capacity'  => 'required|numeric|min:0',
        ]);

        $restaurant = new Restaurant();
        $restaurant->name = $request->input('name');
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/restaurants');
            $restaurant->image = basename($image);
        } else {
            $restaurant->image = '';
        }
        $restaurant->description      = $request->input('description');
        $restaurant->lowest_price     = $request->input('lowest_price');
        $restaurant->highest_price    = $request->input('highest_price');
        $restaurant->postal_code      = $request->input('postal_code');
        $restaurant->address          = $request->input('address');
        $restaurant->opening_time     = $request->input('opening_time');
        $restaurant->closing_time     = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');
        $restaurant->save();
        // カテゴリ
        $category_ids = array_filter($request->input('category_ids'));
        $restaurant->categories()->sync($category_ids);
        // 休日
        $regular_holiday_ids = array_filter($request->input('regular_holiday_ids'));
        $restaurant->regular_holidays()->sync($regular_holiday_ids);

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Restaurant  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Restaurant $restaurant)
    {
        return view('admin.restaurants.show', compact('restaurant'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Restaurant  $restaurants
     * @return \Illuminate\Http\Response
     */
    public function edit(Restaurant $restaurant)
    {
        // インスタンスに紐づくcategoriesテーブルのすべてのデータをインスタンスのコレクションとして取得する
        $categories = Category::all();
        // 設定されたカテゴリのIDを配列化する
        $category_ids = $restaurant->categories->pluck('id')->toArray();

        // 休日データ
        $regular_holidays = RegularHoliday::all();

        return view('admin.restaurants.edit', compact('restaurant', 'categories', 'category_ids', 'regular_holidays'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @param  \App\Models\Restaurant  $restaurants
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'name'             => 'required',
            'image'            => 'image|max:2048',
            'description'      => 'required',
            'lowest_price'     => 'required|numeric|min:0|lte:highest_price',
            'highest_price'    => 'required|numeric|min:0|gte:lowest_price',
            'postal_code'      => 'required|digits:7',
            'address'          => 'required',
            'opening_time'     => 'required|before:closing_time',
            'closing_time'     => 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        $restaurant->name = $request->input('name');
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/restaurants');
            $restaurant->image = basename($image);
        }
        $restaurant->description      = $request->input('description');
        $restaurant->lowest_price     = $request->input('lowest_price');
        $restaurant->highest_price    = $request->input('highest_price');
        $restaurant->postal_code      = $request->input('postal_code');
        $restaurant->address          = $request->input('address');
        $restaurant->opening_time     = $request->input('opening_time');
        $restaurant->closing_time     = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');
        $restaurant->save();

        // カテゴリ
        $category_ids = array_filter($request->input('category_ids'));
        $restaurant->categories()->sync($category_ids);
        // 休日
        $regular_holiday_ids = array_filter($request->input('regular_holiday_ids'));
        $restaurant->regular_holidays()->sync($regular_holiday_ids);

        return redirect()->route('admin.restaurants.show', $restaurant)->with('flash_message', '店舗を編集しました。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
    }
}
