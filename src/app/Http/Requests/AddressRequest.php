<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->has('name')){
            // フォームに名前の入力欄がある場合
            return [
                'name' => ['required'],
                'post' => ['required', 'regex:/^\d{3}[-]\d{4}$/'],
                'address' => ['required'],
                'building' => ['required']
            ];
        }else{
            // フォームに名前の入力欄がない場合
            return [
                'post' => ['required','regex:/^\d{3}[-]\d{4}$/'],
                'address' => ['required'],
                'building' => ['required']
            ];
        }
    }
}
