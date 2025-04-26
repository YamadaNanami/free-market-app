@extends('layouts.app')

@section('title','商品詳細画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="grid-wrap">
    <div class="img-wrap">
        <img src="{{ asset('storage/img/'.$item['img_url']) }}" alt="商品画像" class="item-img">
    </div>
    <div class="detail-area">
        <section class="detail-sec">
            <h2 class="page-title">{{$item['item_name']}}</h2>
            <p class="brand-name">{{$item['brand_name']}}</p>
            <p class="price">¥{{ number_format($item['price']) }} (税込)</p>
            <div class="icon-flex">
                <form action="{{ route('like', ['item_id' => $item['id']]) }}" method="post" class="like-form">
                    @csrf
                    <input type="hidden" name="like" value="{{ $like['hasLikedItem'] }}">
                    <button type="submit" class="like-btn">
                        <img src="{{ asset('storage/img/icon-like.svg') }}" alt="いいねアイコン" class="like-img @if($like['hasLikedItem']) active @endif">
                    </button>
                    <p class="pieces">{{ $like['count'] }}</p>
                </form>
                <div class="cmt-link">
                    <a href="#cmt-area" class="cmt-icon">
                        <img src="{{ asset('storage/img/icon-cmt.svg') }}" alt="コメントアイコン" class="cmt-img">
                    </a>
                    <p class="pieces">{{ $comment['count'] }}</p>
                </div>
            </div>
            <form action="/purchase/:{{ $item['id'] }}" method="get" class="purchase-form">
                <button type="submit" class="purchase-btn">購入手続きへ</button>
            </form>
        </section>
        <section class="detail-sec">
            <h3 class="sec-title">商品説明</h3>
            <p class="item-info">{{ $item['description'] }}</p>
        </section>
        <section class="detail-sec">
            <h3 class="sec-title">商品の情報</h3>
            <div class="item-detail">
                <h4 class="detail-title">カテゴリー</h4>
                <div class="category-area">
                    @foreach($categories as $category)
                    <p class="category">{{$category['category']}}</p>
                    @endforeach
                </div>
            </div>
            <div class="item-detail">
                <h4 class="detail-title">商品の状態</h4>
                <p class="condition">
                    @switch($item['condition'])
                        @case(1)
                            良好
                            @break
                        @case(2)
                            目立った傷や汚れなし
                            @break
                        @case(3)
                            やや傷や汚れあり
                            @break
                        @default
                            状態が悪い
                    @endswitch
                </p>
            </div>
        </section>
        <section class="detail-sec" id="cmt-area">
            <h3 class="sec-title">コメント({{ $comment['count'] }})</h3>
            @if($comment['status'] == 'has_comments')
            <!-- 商品に対してコメントが存在する時のみ表示させる -->
            <div class="user-img-wrap">
                <img src="{{ asset('storage/img/'.$comment['user']['profile']['img_url']) }}" alt="プロフィール画像" class="user-img">
                <p class="user-name">{{ $comment['user']['name']}}</p>
            </div>
            <p class="cmt">{{ $comment['comment'] }}</p>
            @endif
            <form action="{{ route('comment', ['item_id' => $item['id']]) }}" method="post" class="cmt-form">
                @csrf
                <h4 class="sub-title">商品へのコメント</h4>
                <textarea name="comment" class="form-input" rows="10">{{ old('comment') }}</textarea>
                @error('comment')
                <p class="error-msg">{{ $message }}</p>
                @enderror
                <button type="submit" class="cmt-btn">コメントを送信する</button>
            </form>
        </section>
    </div>
</div>

@endsection
