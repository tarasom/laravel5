<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Auth\AuthorizationService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/profile';

    public function login(LoginRequest $request)
    {
        $authService = new AuthorizationService();

        $accessToken = $authService->getAccessToken($request->code, route('main'));

        session()->flash('access_token', $accessToken);

        return strlen($accessToken) > 0 ? redirect()->intended($this->redirectPath()) : redirect('/');
    }
}
