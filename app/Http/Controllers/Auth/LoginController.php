<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Illuminate\Foundation\Auth\AuthenticatesUsers
     * 
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username() // このメソッドを追記
    {
        return 'name';
    }

    // ★ メソッド追加 リダイレクトさせるのでなくユーザー情報を返却する
    protected function authenticated(Request $request, $user)
    {
        return $user;
    }

    // ログアウト
    protected function loggedOut(Request $request)
    {
        // セッションを再生成する
        $request->session()->regenerate();

        return response()->json();
    }
}