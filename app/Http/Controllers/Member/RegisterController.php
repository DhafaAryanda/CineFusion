<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        return view('member.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required|numeric',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        $data = $request->except("_token");

        $isEmailExist = User::where('email', $request->email)->exists();
        
        if ($isEmailExist) {
            return back()->withErrors([
                'email' => 'This email is already registered'
            ])->withInput();
        }

        $data['role'] = 'member';
        $data['password'] = Hash::make($request->password);

        User::create($data);

        return back();
        // return redirect()->route('member.login')->with('success', 'Register success');

    }
}
