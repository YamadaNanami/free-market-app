@extends('layouts.app')

@section('title','プロフィール画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<section class="profile-sec">
    <div class="img-wrap">
        <img src="{{ asset('storage/img/'.$user['profile']['img_url']) }}" alt="プロフィール画像" class="img-area">
        <input type="text" name="name" class="input-name" readonly value="{{ $user['name'] }}">
    </div>
    <form action="/mypage/profile" method="get" class="update-form">
        <button type="submit" class="update-btn">プロフィールを編集</button>
    </form>
</section>
<section class="profile-sec">
    <div class="content-header">
        <form action="/mypage" method="get" class="item-list-form">
            <input type="radio" onchange="submit(this.form)" name="page" value="sell" id="sell" hidden checked>
            <label for="sell" class="tab-txt">出品した商品</label>
            <input type="radio" onchange="submit(this.form)" name="page" value="buy" id="buy" hidden @isset($page)@if($page == 'buy')checked @endif @else''@endisset>
            <label for="buy" class="tab-txt">購入した商品</label>
        </form>
    </div>
    <ul class="item-list">
        @foreach($items as $item)
        <li class="item">
            <a href="/item/:{{ $item['id'] }}">
                <div class="item-img-wrap">
                    <img src="{{ asset('storage/img/'.$item['img_url']) }}" alt="商品画像" class="item-img">
                </div>
                <div class="item-name">{{$item['item_name']}}</div>
            </a>
        </li>
        @endforeach
    </ul>
</section>
@endsection