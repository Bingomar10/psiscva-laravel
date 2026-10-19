<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Usar 'nombre_usuario' en lugar de 'email' para el login.
     */
    public function username()
    {
        return 'nombre_usuario';
    }

    /**
     * Validar también que el usuario esté activo.
     */
    protected function credentials(\Illuminate\Http\Request $request)
    {
        return array_merge(
            $request->only($this->username(), 'password'),
            ['activo' => 1]
        );
    }
}
