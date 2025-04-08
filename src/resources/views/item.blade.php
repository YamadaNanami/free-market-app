@extends('layouts.app')

@section('title','商品詳細画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

<!-- @section('action','/logout')
@section('btn-name','ログアウト') -->

@section('content')
<div class="grid-wrap">
    <div class="item-img">
        <img src="" alt="商品画像">
    </div>
    <div class="detail-area">
        <section class="detail-sec">
            <h2 class="page-title">商品名がここに入る</h2>
            <p class="brand-name">ブランド名</p>
            <p class="price">¥47,000 (税込)</p>
            <form action="" method="post" class="like-form">
                @csrf
                <input type="hidden" name="item_id">
                <input type="submit" value="" class="like-icon">
                <input type="number" name="" readonly class="pieces">
            </form>
            <div class="cmt-link">
                <a href="#cmt-area" class="cmt-icon"></a>
                <p class="pieces"></p>
            </div>
            <form action="/purchase/:item_id" method="post" class="purchase-form">
                @csrf
                <!-- 購入手続きのために商品IDを取得する -->
                <input type="hidden" name="item_id">
                <button type="submit" class="purchase-btn">購入手続きへ</button>
            </form>
        </section>
        <section class="detail-sec">
            <h3 class="sec-title">商品説明</h3>
            <p class="item-info">カラー：グレー</p>
            <p class="item-info">新品<br>商品の状態は良好です。傷もありません。</p>
            <p class="item-info">購入後、即発送いたします。</p>
        </section>
        <section class="detail-sec">
            <h3 class="sec-title">商品の情報</h3>
            <div class="item-detail">
                <h4 class="detail-title">カテゴリー</h4>
                <div class="category-area">
                    <p class="category">洋服</p>
                    <p class="category">メンズ</p>
                </div>
            </div>
            <div class="item-detail">
                <h4 class="detail-title">商品の状態</h4>
                <p class="condition">良好</p>
            </div>
        </section>
        <section class="detail-sec" id="cmt-area">
            <h3 class="sec-title">コメント(1)</h3>
            <div class="user-img-wrap">
                <img src="" alt="プロフィール画像" class="user-img">
                <p class="user-name">admin</p>
            </div>
            <p class="cmt">こちらにコメントが入ります。</p>
            <form action="" method="post" class="cmt-form">
                @csrf
                <h4 class="sub-title">商品へのコメント</h4>
                <textarea name="comment" id="cmt" class="form-input" rows="10"></textarea>
                <button type="submit" class="cmt-btn">コメントを送信する</button>
            </form>
        </section>
    </div>
</div>

@endsection
