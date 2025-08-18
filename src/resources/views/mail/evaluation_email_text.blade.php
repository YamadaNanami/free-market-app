取引完了メール
購入者が取引評価を完了させました。
対象の取引チャット画面より、取引評価を実施してください！

取引ID：{{ $trade['id'] }}
取引商品名：{{ $tradeItemName }}

対象の取引チャット画面はこちら⇨{{ route('chat.index',['trade_id' =>  $trade['id']]) }}