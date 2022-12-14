<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\Response as Controller;
use App\Models\Members;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login','register']]);
  }
  public function login(Request $request)
  {
    $input = $request->all();
    $validator = Validator::make($input,[
      'email' => 'required|string|email',
      'password' => 'required|string',
    ]);

    if($validator->fails()) {
      return $this->handleError('Validation Error', $validator->errors());       
    }

    $member = Members::firstWhere('email', $request->email);

    if (!$member) {
      return $this->handleError('Member was not found');
    }
    if (!Hash::check($request->password, $member->password)) {
      return $this->handleError('Password is incorrect');
    }

    // $credential = $member->only('email', 'password');

    $token = Auth::login($member);
    if (!$token) {
      return $this->handleError('Unauthorized');
    }
    $cookie = cookie('session', $token, 60 * 24);
    $response = [
      'name' => $member->name,
      'username' => $member->username,
      'email' => $member->email,
      'token' => $token,
    ];

    return $this->handleResponse($response, 'Member Login successfully')->withCookie($cookie);
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
