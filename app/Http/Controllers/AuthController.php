<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'name' => 'required',
          'email' => 'required|email|unique:users',
          'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(422, "Unprocessable Entity", "Validation fails.", $validator));
        }

         $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => Hash::make(request('password')),
        ]);

        if ($user) {
            return response()->json(responseFormatter(200, 'OK', 'Success', $user));
        } else {
            return response()->json(responseFormatter(422, 'FAILED', 'Register Failed', $user));
        }
    }

    public function login(Request $request)
    {
        $error = basicValidation($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

          if ($error) {
            return response()->json(responseFormatter(422, "Unprocessable Entity", "Validation fails.", $error));
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(responseFormatter(422, "Unprocessable Entity", "The provided credentials are incorrect.", ['email' => ['The provided credentials are incorrect.'],]));
        }

        return 'test';
    }

    public function test()
    {
        $user = User::first();
        $attendance = Attendance::where('user_id', $user->id)->paginate(10);
        return response()->json(responseFormatter(200, 'OK', 'Success', $attendance));
    }
}
