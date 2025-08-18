<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EvaluationEmail extends Mailable
{
    public $recipient;
    public $trade;
    public $tradeItemName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tradeSql)
    {
        $this->recipient = $tradeSql->seller()->first();
        $this->trade = $tradeSql;
        $this->tradeItemName = $tradeSql->item()->value('item_name');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->recipient['email']) // 宛先
            ->subject('取引完了メール')// 件名
            ->view('mail.evaluation_email') // 本文（HTMLメール）
            ->text('mail.evaluation_email_text'); // 本文（プレーンテキストメール）
    }
}
