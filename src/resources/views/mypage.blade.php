@extends('layouts.app')

@section('title','プロフィール画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<section class="profile-sec">
    <div class="profile-wrap">
        <img src="@if(is_null($user['profile']) || is_null($user['profile']['img_url'])) {{ asset('storage/img/noImage.png') }} @else{{ asset('storage/img/'.$user['profile']['img_url']) }}@endif" alt="プロフィール画像" class="img-area">
        <div class="flex-area">
            <input type="text" name="name" class="input-name" readonly value="{{ $user['name'] }}">
            <div class="evaluations-area">
                <!-- 評価がある場合は、取引評価の平均を表示する -->
                @if(!is_null($getAvgEvaluation))
                    @for($i = 1; $i <= $getAvgEvaluation; $i++)
                        <img src="{{ asset('storage/img/star.svg') }}" alt="ユーザー評価" class="star active">
                    @endfor
                    @for($i = 1; $i <= 5 - $getAvgEvaluation; $i++)
                        <img src="{{ asset('storage/img/star.svg') }}" alt="ユーザー評価" class="star">
                    @endfor
                @endif
            </div>
        </div>
    </div>
    <form action="/mypage/profile" method="get" class="update-form">
        <button type="submit" class="update-btn">プロフィールを編集</button>
    </form>
</section>
<section class="profile-sec">
    <div class="content-header">
        <form action="/mypage" method="get" class="item-list-form">
            <input type="radio" onchange="submit(this.form)" name="tab" value="sell" id="sell" hidden checked>
            <label for="sell" class="tab-txt">出品した商品</label>
            <input type="radio" onchange="submit(this.form)" name="tab" value="buy" id="buy" hidden @isset($tab)@if($tab == 'buy')checked @endif @endisset>
            <label for="buy" class="tab-txt">購入した商品</label>
            <input type="radio" onchange="submit(this.form)" name="tab" value="trade" id="trade" hidden @isset($tab)@if($tab == 'trade')checked @endif @endisset>
            <label for="trade" class="tab-txt">取引中の商品
                @if($totalNotice != 0)
                <!-- 通知がない場合は通知バッチを表示しない -->
                    <span class="notice">{{ $totalNotice }}</span>
                @endif
            </label>
        </form>
    </div>
    <ul class="item-list">
        @foreach($items as $item)
        <li class="item">
            <!-- 取引中の商品を表示する場合のみ、チャット画面を表示できるようにする -->
            <a href="{{ empty($tab) || $tab != 'trade' ? route('item.detail',['item_id' => $item['id']]) : route('chat.index',['trade_id' => $item['tradeId']]) }}">
                <div class="item-img-wrap">
                    <img src="{{ asset('storage/img/'.$item['img_url']) }}" alt="商品画像" class="item-img">
                    <!-- 通知がある場合のみ、通知バッチを表示する -->
                    @isset($tab)
                        @if($tab == 'trade' && $item['notice'] != 0)
                            <span class="item-notice">{{ $item['notice'] }}</span>
                        @endif
                    @endisset
                </div>
                <div class="item-name">{{$item['item_name']}}</div>
            </a>
        </li>
        @endforeach
    </ul>
</section>
@endsection