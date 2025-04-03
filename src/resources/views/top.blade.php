@extends('layouts.app')

@section('title','商品一覧画面（トップ画面）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/top.css') }}">
@endsection

<!-- ログインとログアウトの表示については後で調整する -->
@section('action','/login')
@section('btn-name','ログイン')

@section('content')
<div class="content-header">
    <!-- 文字色の処理後で追加する -->
    <form action="/" method="get" class="item-list-form">
        <input type="submit" class="list-item" value="おすすめ">
    </form>
    <form action="/?page=mylist" method="get" class="item-list-form">
        <input type="submit" class="list-item" value="マイリスト">
    </form>
</div>

<ul class="item-list">
    <!-- for文で処理する -->
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
@endsection