<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SellController extends Controller
{
    public function index(){
        $categories = Category::all();
        return view('/sell',compact('categories'));
    }

    public function saveTempImg(ExhibitionRequest $request){
        if($oldImg = session()->get('itemImg')){
            // セッションとtempディレクトリに保存済みの場合は削除する
            Storage::disk('public')->delete('img/temp/' . $oldImg);
            session()->forget('itemImg');
        }

        $tempImg = $request->file('img_url');

        // 画像名を一意にする
        $imgName = Str::uuid().$tempImg->getClientOriginalName();
        $tempImg->storeAs('public/img/temp', $imgName);

        // セッションに画像名を保存する
        session()->put('itemImg', $imgName);

        return redirect()->back();

    }

    public function sell(ExhibitionRequest $request){
        $id = Auth::id();
        $itemImg = session()->get('itemImg');

        if(!$itemImg){
            // 商品画像のバリデーションチェックはsaveTempImgで実施するため、ここで値があるかだけ確認する
            return redirect()->back()->withInput();
        }

        $item = [
            'user_id' => $id,
            'img_url' => 'item_img/'.$itemImg,
            'item_name' => $request->item_name,
            'brand_name' => $request->brand_name,
            'price' => $request->price,
            'description' => $request->description,
            'condition' => $request->condition
        ];

        // tempに保存した画像をitem_imgに移動させる
        Storage::disk('public')->move('img/temp/' . $itemImg, 'img/item_img/' . $itemImg);
        // img_urlをセッションから削除する
        session()->forget('itemImg');

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
