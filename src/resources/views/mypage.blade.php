@extends('layouts.app')

@section('title','プロフィール画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('action','/logout')
@section('btn-name','ログアウト')

@section('content')
<section class="profile-sec">
    <div class="img-wrap">
        <img src="" alt="プロフィール画像" class="img-area">
        <input type="text" name="name" class="input-name" readonly value="ユーザー名">
    </div>
    <form action="/mypage/profile" method="get" class="update-form">
        @csrf
        <!-- ユーザ情報を取得するためにid？を取得＆リクエストで送信 -->
        <input type="hidden" name="">
        <button type="submit" class="update-btn">プロフィールを編集</button>
    </form>
</section>
<section class="profile-sec">
    <div class="content-header">
        <!-- 文字色の処理後で追加する -->
        <form action="/mypage?page=sell" method="" class="item-list-form">
            <input type="submit" class="list-item" value="出品した商品">
        </form>
        <form action="/mypage?page=buy" class="item-list-form">
            <input type="submit" class="list-item" value="購入した商品">
        </form>
    </div>
    <ul class="item-list">
        <!-- for文で処理する -->
        <li class="item">
            <a href="/item/:item_id">
                <div class="item-img-wrap">
                    <img src="" alt="商品画像" class="item-img">
                </div>
                <div class="item-name">商品名</div>
            </a>
        </li>
        <li class="item">
            <a href="">
                <div class="item-img-wrap">
                    <img src="" alt="商品画像" class="item-img">
                </div>
                <div class="item-name">商品名</div>
            </a>
        </li>
        <li class="item">
            <a href="">
                <div class="item-img-wrap">
                    <img src="" alt="商品画像" class="item-img">
                </div>
                <div class="item-name">商品名</div>
            </a>
        </li>
        <li class="item">
            <a href="">
                <div class="item-img-wrap">
                    <img src="" alt="商品画像" class="item-img">
                </div>
                <div class="item-name">商品名</div>
            </a>
        </li>
        <li class="item">
            <a href="/item/">
                <div class="item-img-wrap">
                    <img src="" alt="商品画像" class="item-img">
                </div>
                <div class="item-name">商品名</div>
            </a>
        </li>
        <li class="item">
            <a href="">
                <div class="item-img-wrap">
                    <img src="" alt="商品画像" class="item-img">
                </div>
                <div class="item-name">商品名</div>
            </a>
        </li>
        <!-- for文ここまで -->
    </ul>
</section>
@endsection