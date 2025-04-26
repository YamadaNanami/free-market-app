<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $id = Auth::id();
        $item = [
            'user_id' => $id,
            // img_url後で直す
            'img_url' => 'item_img/Armani+Mens+Clock.jpg',
            'item_name' => $request->item_name,
            'brand_name' => $request->brand_name,
            'price' => $request->price,
            'description' => $request->description,
            'condition' => $request->condition
        ];

        DB::transaction(static function () use ($item,$request) {
            Item::create($item);
            $lastId = Item::orderByDesc('id')->first()->id;

            // 登録した商品とカテゴリーを紐付ける
            foreach($request->categories as $category){
                Category::find($category)->items()->attach($lastId);
            };
        });

        return redirect('mypage');
    }
}
