<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
        if($this->has('img_url')){
            return [
                'img_url' => ['required','mimes:jpeg,png'],
            ];
        }else{
            return [
                'item_name' => ['required'],
                'description' => ['required', 'max:255'],
                'categories' => ['required', 'array'],
                'condition' => ['required'],
                'price' => ['required', 'integer', 'min:0', 'regex:/^[0-9]+$/']
            ];
        }
    }
}