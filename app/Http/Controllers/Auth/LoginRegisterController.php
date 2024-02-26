<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginRegisterController extends Controller
{
    public function __construct(){
        $this->middleware('guest')->except([
            'logout', 'dashboard'
        ]);
    }

    public function register(){
        return view('register');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:100',
            'email'=> 'required|email|max:200|unique:users',
            'password' => 'required|min:5'
        ]);

        User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password)
        ]);

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();
        return redirect()->route('dashboard')
        ->withSuccess('You have successfully registered & logged in!');

    }

    public function login(){
        return view('login');
    }

    public function authenticate(Request $request){
        $credentials = $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            return redirect()->route('dashboard')->withSuccess('You have successfully logged in');
        }
        
        return back()->withErrors([
            'email' => 'Your provided credidentials do not match with record.',
        ])->onlyInput('email');
    }

    public function dashboard(){
        if(Auth::check()){
            return view('dashboard');
        }

        return redirect()->route('login')->withErrors([
            'email'=>'Please login to access the dashboard',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->withSuccess('You have logged out successfully!');
    } 
}
