<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result = [], $message)
    {
        $response = [
            'status'    => true,
            'data'      => $result,
            'message'   => $message
        ];

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 401)
    {
        $response = [
            'status'    => false,
            'message'   => $error,
            'errors'    => $errorMessages
        ];

        return response()->json($response, $code);
    }
}
