@extends('layouts.app')

@section('title','商品一覧画面（トップ画面）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/top.css') }}">
@endsection

@section('content')
<div class="content-header">
    <!-- 文字色の処理後で追加する -->
    <form action="/" method="get" class="item-list-form">
        <input type="radio" onchange="submit(this.form)" name="page" value="" id="rec" hidden @if($currentTab == 'default') checked @endif>
        <label for="rec" class="tab-txt">おすすめ</label>
        <input type="radio" onchange="submit(this.form)" name="page" value="mylist" id="mylist" hidden @if($currentTab == 'mylist') checked @endif>
        <label for="mylist" class="tab-txt">マイリスト</label>
    </form>
</div>

@if($currentTab == 'mylist' && !Auth::check())
@endif
<ul class="item-list">
    @foreach($items as $item)
    <li class="item">
        <a href="/item/:{{ $item['id'] }}">
            <div class="item-img-wrap">
                <img src="{{ asset('storage/img/'.$item['img_url']) }}" alt="商品画像" class="item-img">
            </div>
            <p class="item-name">{{$item['item_name']}}@if($item['soldOutItemExists']){{ '　Sold' }}@endif</p>
        </a>
    </li>
    @endforeach
</ul>
@endsection