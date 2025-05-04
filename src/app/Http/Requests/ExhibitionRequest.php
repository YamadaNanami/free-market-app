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
        return [
            'item_name' => ['required'],
            'description' => ['required','max:255'],
            'img_url' => ['required','mimes:jpeg,png','extensions:jpeg,png'],
            'categories' => ['required','array'],
            'condition' => ['required'],
            'price' => ['required','integer','min:0','regex:/^[0-9]+$/']
        ];
    }
}
