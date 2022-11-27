<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\Response as Controller;
use App\Models\Members;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login','register']]);
  }

  public function register(Request $request)
  {
    $input = $request->all();
    $validator = Validator::make($input,[
      'name' => 'required|string|max:255',
      'username' => 'required|string',
      'email' => 'required|string|email|max:255|unique:members',
      'password' => 'required|string|min:6',
    ]);

    if($validator->fails()) {
      return $this->handleError('Validation Error.', $validator->errors());       
    }

    $member = Members::create([
      'name' => $request->name,
      'username' => $request->username,
      'email' => $request->email,
      'password' => bcrypt($request->password),
    ]);

    $token = Auth::login($member, $remember = true);
    $response = [
      'name' => $member->name,
      'username' => $member->username,
      'email' => $member->email,
      'token' => $token,
    ];
    return $this->handleResponse($response, 'User created successfully');
  }
}
