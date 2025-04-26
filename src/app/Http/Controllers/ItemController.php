<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index($item_id){
        // 商品情報を取得
        $item = Item::with(['categories','comments'])->find($item_id);

        // カテゴリーをid順にソート
        $categories = $item->categories->sortBy('id');

        // コメントの件数をカウント
        $countCmt = count($item->comments);

        // 商品コメントを1件取得（コメント内容とコメントしたユーザー情報）
        $comment = $item->comments()->first();

        // いいねの件数をカウント
        $countLike = Item::find($item_id)->like()->count();

        // ユーザーが商品にいいねしているかチェック
        if(Auth::check()){
            $hasLikedItem = User::find(Auth::id())->like()->where('item_id', $item_id)->exists();
        }else{
            $hasLikedItem = false;
        }

        if($countCmt > 0){
            // 商品にコメントがある場合
            $comment = [
                'count' => $countCmt,
                'user' => User::with('profile')->find($comment->pivot->user_id),
                'comment' => $comment->pivot->comment,
                'status' => 'has_comments'
            ];
        }else{
            // 商品にコメントがない場合
            $comment = [
                'count' => $countCmt,
                'status' => 'no_comments'
            ];
        }

        $like = [
            'count' => $countLike,
            'hasLikedItem' => $hasLikedItem
        ];

        return view('item',compact('item','categories','comment','like'));
    }

    public function createComment($item_id,CommentRequest $request){
        if(Auth::check()){
            // ログイン済の場合にコメント送信を有効にする
            $user_id = Auth::id();
            $comment = $request->comment;

            // commentsテーブルに追加
            $item = Item::find($item_id);
            $item->comments()->attach([$user_id =>['comment' => $comment]]);

            return redirect()->back();
        }
    }

    public function toggleLike($item_id,Request $request){
        if(Auth::check()){
            // ログイン済の場合にいいね機能を有効にする
            $user_id = Auth::id();
            $hasLikedItem = $request->like;

            if($hasLikedItem){
                // 既にいいねしている場合
                User::find($user_id)->like()->detach($item_id);
            }else{
                // いいねしていない場合
                User::find($user_id)->like()->attach($item_id);
            }
        }

        return redirect()->back();
    }

}