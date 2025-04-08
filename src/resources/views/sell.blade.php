@extends('layouts.app')

@section('title','商品出品画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<h2 class="page-title">商品の出品</h2>
<form action="/sell" method="post" class="sell-form">
    @csrf
    <section class="form-sec">
        <h4 class="sub-title">商品画像</h4>
        <img src="" alt="プロフィール画像" class="img-area">
        <label for="img-file" class="img-label">
            画像を選択する
            <input type="file" name="img-file" id="img-file" accept="image/*" class="input-img">
        </label>
    </section>
    <section class="form-sec">
        <h3 class="sec-title">商品の詳細</h3>
        <h4 class="sub-title">カテゴリー</h4>
        <div class="flex-area">
            <input type="checkbox" name="category" id="fashion" class="form-checkbox">
            <label for="fashion" class="checkbox-label">ファッション</label>
            <input type="checkbox" name="category" id="appliances" class="form-checkbox">
            <label for="appliances" class="checkbox-label">家電</label>
            <input type="checkbox" name="category" id="interior" class="form-checkbox">
            <label for="interior" class="checkbox-label">インテリア</label>
            <input type="checkbox" name="category" id="ladies" class="form-checkbox">
            <label for="ladies" class="checkbox-label">レディース</label>
            <input type="checkbox" name="category" id="men" class="form-checkbox">
            <label for="men" class="checkbox-label">メンズ</label>
            <input type="checkbox" name="category" id="cosmetics" class="form-checkbox">
            <label for="cosmetics" class="checkbox-label">コスメ</label>
            <input type="checkbox" name="category" id="book" class="form-checkbox">
            <label for="book" class="checkbox-label">本</label>
            <input type="checkbox" name="category" id="game" class="form-checkbox">
            <label for="game" class="checkbox-label">ゲーム</label>
            <input type="checkbox" name="category" id="sports" class="form-checkbox">
            <label for="sports" class="checkbox-label">スポーツ</label>
            <input type="checkbox" name="category" id="kitchen" class="form-checkbox">
            <label for="kitchen" class="checkbox-label">キッチン</label>
            <input type="checkbox" name="category" id="handmade" class="form-checkbox">
            <label for="handmade" class="checkbox-label">ハンドメイド</label>
            <input type="checkbox" name="category" id="accessory" class="form-checkbox">
            <label for="accessory" class="checkbox-label">アクセサリー</label>
            <input type="checkbox" name="category" id="toy" class="form-checkbox">
            <label for="toy" class="checkbox-label">おもちゃ</label>
            <input type="checkbox" name="category" id="baby" class="form-checkbox">
            <label for="baby" class="checkbox-label">ベビー・キッズ</label>
        </div>
        <h4 class="sub-title">商品の状態</h4>
        <select name="condition" class="form-input">
            <option value="" disabled selected>選択してください</option>
            <option value="1" >良好</option>
            <option value="2" >目立った傷や汚れなし</option>
            <option value="3" >やや傷や汚れあり</option>
            <option value="4" >状態が悪い</option>
        </select>
    </section>
    <section class="form-sec">
        <h3 class="sec-title">商品名と説明</h3>
        <h4 class="sub-title">商品名</h4>
        <input type="text" name="name" class="form-input">
        <h4 class="sub-title">ブランド名</h4>
        <input type="text" name="brand" class="form-input">
        <h4 class="sub-title">商品の説明</h4>
        <textarea name="description" class="form-input" rows="5"></textarea>
        <h4 class="sub-title">販売価格</h4>
        <input type="text" name="price" class="form-input" value="¥ ">
    </section>
    <button type="submit" class="sell-form-btn">出品する</button>
</form>

@endsection
