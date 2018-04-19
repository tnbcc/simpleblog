<?php

namespace App\Http\Requests;

class StatusesRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
             'content' => 'required|max:140',
        ];
    }
}
