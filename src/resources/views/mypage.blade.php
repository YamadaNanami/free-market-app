@extends('layouts.app')

@section('title','プロフィール画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<section class="profile-sec">
    <div class="img-wrap">
        <img src="{{ asset('storage/'.$user['profile']['img_url']) }}" alt="プロフィール画像" class="img-area">
        <input type="text" name="name" class="input-name" readonly value="{{ $user['name'] }}">
    </div>
    <form action="/mypage/profile" method="get" class="update-form">
        <button type="submit" class="update-btn">プロフィールを編集</button>
    </form>
</section>
<section class="profile-sec">
    <div class="content-header">
        <!-- 文字色の処理後で追加する -->
        <form action="/mypage" method="get" class="item-list-form">
            <input type="hidden" name="page" value="sell">
            <input type="submit" class="list-item" value="出品した商品">
        </form>
        <form action="/mypage" method="get" class="item-list-form">
            <input type="hidden" name="page" value="buy">
            <input type="submit" class="list-item" value="購入した商品">
        </form>
    </div>
    <ul class="item-list">
        @foreach($items as $item)
        <li class="item">
            <a href="/item/:{{ $item['id'] }}">
                <div class="item-img-wrap">
                    <img src="{{ asset('storage/'.$item['img_url']) }}" alt="商品画像" class="item-img">
                </div>
                <div class="item-name">{{$item['item_name']}}</div>
            </a>
        </li>
        @endforeach
    </ul>
</section>
@endsection