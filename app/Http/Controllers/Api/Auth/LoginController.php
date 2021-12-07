<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function auth;
use function response;

class LoginController extends Controller
{
    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if(!auth()->attempt($this->credentials($request), $request->input('remember'))){
            throw new AuthenticationException('Email or password is not valid');
        }
        return response()->json([
            'token' => auth()->user()->createToken('Laravel Password Grant Client')->plainTextToken
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }

    private function credentials(LoginRequest $request) : array
    {
        if (is_numeric($request->input('email'))){
            return ['phone' => $request->input('email'), 'password' => $request->input('password')];
        }elseif (filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            return ['email' => $request->input('email'), 'password' => $request->input('password')];
        }else{
            return ['username' => $request->input('email'), 'password' => $request->input('password')];
        }
    }
}
