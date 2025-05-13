@extends('layouts.app')

@section('title','商品購入画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-wrap">
    <div class="detail">
        <section class="purchase-sec">
            <img src="{{ asset('storage/img/'.$item['img_url']) }}" alt="商品画像" class="item-img">
            <div class="item-txt">
                <h2 class="page-title">{{$item['item_name']}}</h2>
                <p class="price">¥ {{ number_format($item['price']) }}</p>
            </div>
        </section>
        <section class="purchase-sec">
            <h3 class="sec-title">支払い方法</h3>
            <form action="/purchase/payment" method="post">
                @csrf
                <select onchange="submit(this.form)" name="payment" class="payment">
                    <option disabled selected>選択してください</option>
                    <option value="konbini" {{ session()->get('payment') =='konbini'?'selected':'' }}>コンビニ払い</option>
                    <option value="card" {{ session()->get('payment') =='card'?'selected':'' }}>カード支払い</option>
            </select>
            </form>
        </section>
        <section class="purchase-sec">
            <div class="flex-wrap">
                <h3 class="sec-title">配送先</h3>
                <a href="{{ route('address.edit',['item_id' => $item['id']]) }}" class="edit-link">変更する</a>
            </div>
            <p class="post">〒 {{$user['post']}}</p>
            <p class="address">{{ $user['address'] }} {{ $user['building'] }}</p>
        </section>
    </div>
    <section class="subtotal">
        <table class="subtotal-tb">
            <tr class="tb-row">
                <th class="tb-header">商品代金</th>
                <td class="tb-detail">¥ {{ number_format($item['price']) }}</td>
            </tr>
            <tr class="tb-row">
                <th class="tb-header">支払い方法</th>
                <td class="tb-detail">
                    @switch(session()->get('payment'))
                        @case ('konbini')
                            コンビニ払い
                            @break
                        @case ('card')
                            カード払い
                            @break
                        @default
                            @break
                    @endswitch
                </td>
            </tr>
        </table>
        <form action="{{ route('stripe.checkout',['item_id' => $item['id']]) }}" method="post">
            @csrf
            <input type="hidden" name="payment" value="{{ session('payment') }}">
            <input type="hidden" name="address[post]" value="{{ $user['post'] }}">
            <input type="hidden" name="address[address]" value="{{ $user['address'] }}">
            <input type="hidden" name="address[building]" value="{{ $user['building'] }}">
            <button type="submit" class="purchase-btn">購入する</button>
        </form>
    </section>
</div>
@endsection