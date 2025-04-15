<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;

class SellController extends Controller
{
    public function index(){
        $categories = Category::all();
        return view('/sell',compact('categories'));
    }

    public function sell(ExhibitionRequest $request){
        $user = Auth::id();
        // dd($request);
        $item = [
            'user_id' => $user,
            'categories' => json_encode($request->categories),
            // img_url後で直す
            'img_url' => 'aaaaaaa',
            'item_name' => $request->item_name,
            'brand_name' => $request->brand_name,
            'price' => $request->price,
            'description' => $request->description,
            'condition' => $request->condition
        ];
        Item::create($item);
        return view('/top');
    }
}
