<h1>取引完了メール</h1>
<p>購入者が取引評価を完了させました。</p>
<p>対象の取引チャット画面より、取引評価を実施してください！</p>

<p>取引ID：{{ $trade['id'] }}</p>
<p>取引商品名：{{ $tradeItemName }}</p>

<a href="{{ route('chat.index',['trade_id' =>  $trade['id']]) }}">対象の取引チャット画面はこちら</a>