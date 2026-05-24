<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;


class Login extends Component
{
    public $email;
    public $password;
    public $showPassword = false;
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        $credentials = [
            Fortify::username() => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            $user = Auth::user();

            if ($user->user_role === 'emp') {
                return app(LoginResponse::class)->toResponse($this->request)->intended('/home');
            } else if (in_array($user->user_role, ['sa', 'hr', 'sv', 'pa'])) {
                return app(LoginResponse::class)->toResponse($this->request)->intended('/dashboard');
            }
        } else {
            $this->addError('login', 'Invalid credentials.');
        }
    }

    public function render()
    {
        return view('auth.login');
    }

    public function togglePasswordVisibility()
    {
        $this->showPassword = !$this->showPassword;
    }
}

