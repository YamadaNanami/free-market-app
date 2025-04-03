<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-wrap">
            <h1 class="page-logo">
                <a href="/">
                    <img src="{{ asset('storage/img/logo.svg') }}" alt="サイトのロゴ画像" class="logo-img">
                </a>
            </h1>
            <form action="post" class="search-form">
                <input type="search" name="keyword" class="search-form-input" placeholder="なにをお探しですか？" value="">
            </form>
            <form action="@yield('action')" method="post" class="form-wrap">
                <button type="submit" class="form-btn">@yield('btn-name')</button>
            </form>
            <form action="/mypage" method="get" class="form-wrap">
                <button type="submit" class="form-btn">マイページ</button>
            </form>
            <button name="sell" class="sell-btn">出品</button>
        </div>
    </header>

    <main class="page-content">
        @yield('content')
    </main>
</body>

</html>