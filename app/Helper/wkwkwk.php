<?php


namespace App\Helper;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class wkwkwk
{

    public static function Validate(Request $request, array $rules = [], array $message = []){
        $data = $request->all();
        return Validator::make($data, $rules, $message);
    }
}
