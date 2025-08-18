<?php

namespace App\Http\Livewire;

use Livewire\Component;
use \Illuminate\Session\SessionManager;

class InputToSession extends Component
{
    public $inputText = '';
    public $tradeId;

    public function updatedInputText(){
        $userId = auth()->id();

        // 現在の savedTexts を取得（配列で）
        $savedTexts = session()->get('savedTexts', []);

        // 既存データを削除（同じ userId + tradeId）
        if(!empty($savedTexts)){
            $savedTexts = array_values(array_filter($savedTexts, function ($text) use ($userId) {
                return !($text['userId'] == $userId && $text['tradeId'] == $this->tradeId);
            }));
        }

        // 新しい値を追加
        $savedTexts[] = [
            'userId' => $userId,
            'tradeId' => $this->tradeId,
            'message' => $this->inputText
        ];

        session()->put('savedTexts' , $savedTexts);
    }

    public function mount($tradeId){
        $this->tradeId = $tradeId;
        $userId = auth()->id();

        $savedTexts = session()->get('savedTexts', []);
        foreach($savedTexts as $text){
            if($text['userId'] == $userId && $text['tradeId'] == $this->tradeId){
                $this->inputText = $text['message'];
            }
        }
    }

    public function render()
    {
        return view('livewire.input-to-session');
    }
}
