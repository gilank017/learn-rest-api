<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class Response extends Controller
{
  public function handleResponse($result, $message)
    {
    	$response = [
        'success' => true,
        'data'    => $result,
        'message' => $message,
      ];
      return response()->json($response, 200);
    }

    public function handleError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
        'success' => false,
        'message' => $error,
      ];


      if(!empty($errorMessages)){
        $response['data'] = $errorMessages;
      }


      return response()->json($response, $code);
    }
}