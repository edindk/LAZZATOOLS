<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * @param Request $request
     * @param $response
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return response(['message'=> trans($response)]);

    }

    /**
     * @param Request $request
     * @param $response
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response(['error'=> trans($response)], 422);
    }


}
