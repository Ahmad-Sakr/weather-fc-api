<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class RecordRequest extends FormRequest
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
            'rc_date'               => 'required|date|date_format:Y-m-d',
            'city'                  => 'required|exists:cities,name',
            'details'               => 'required',
            'details.*.hour'        => 'required',
            'details.*.cloud'       => 'required',
            'details.*.temp'        => 'required',
            'details.*.min_temp'    => '',
            'details.*.max_temp'    => '',
            'details.*.pressure'    => '',
            'details.*.sea_level'   => '',
            'details.*.humidity'    => '',
            'details.*.wind_speed'  => '',
        ];
    }
}
