@extends('layouts.app')

@section('title','商品購入画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<form action="" method="post" class="purchase-form">
    @csrf
    <div class="detail">
        <section class="purchase-sec">
            <img src="" alt="商品画像" class="item-img">
            <div class="item-txt">
                <h2 class="page-title">商品名</h2>
                <p class="price">¥ 47,000</p>
            </div>
        </section>
        <section class="purchase-sec">
            <h3 class="sec-title">支払い方法</h3>
            <select name="payment" class="payment">
                <option disabled selected>選択してください</option>
                <option value="">コンビニ払い</option>
                <option value="card">カード支払い</option>
            </select>
        </section>
        <section class="purchase-sec">
            <div class="flex-wrap">
                <h3 class="sec-title">配送先</h3>
                <a href="/purchase/address/:item_id" class="edit-link">変更する</a>
            </div>
            <p class="post">〒 XXX-YYYY</p>
            <p class="address">ここには住所と建物が入ります</p>
        </section>
    </div>
    <section class="subtotal">
        <table class="subtotal-tb">
            <tr class="tb-row">
                <th class="tb-header">商品代金</th>
                <td class="tb-detail">¥ 47,000</td>
            </tr>
            <tr class="tb-row">
                <th class="tb-header">支払い方法</th>
                <td class="tb-detail">コンビニ払い</td>
            </tr>
        </table>
        <button type="submit" class="purchase-btn">購入する</button>
    </section>
</form>
@endsection
