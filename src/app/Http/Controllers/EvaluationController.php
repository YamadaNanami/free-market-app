<?php

namespace App\Http\Controllers;

use App\Mail\EvaluationEmail;
use App\Models\Evaluation;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EvaluationController extends Controller
{
    public function sendEvaluation(Request $request,$tradeId){
        $evaluation = $request->evaluations ?? 0;
        $tradeSql = Trade::find($tradeId);

        $isPurchaser = $request->isPurchaser;

        if($isPurchaser){
            //評価をするのが購入者の場合
            $evaluationTargetUserId = $tradeSql->seller()->value('id');
        }elseif(!$isPurchaser){
            //評価をするのが出品者の場合
            $evaluationTargetUserId = $tradeSql->purchaser()->value('id');
        }

        Evaluation::create([
            'user_id' => $evaluationTargetUserId,
            'trade_id' => $tradeId,
            'evaluation' => $evaluation
        ]);

        if($isPurchaser){
            // 出品者へ取引完了メールを送信する
            Mail::send(new EvaluationEmail($tradeSql));
        }

        return redirect()->route('top.index');
    }
}
