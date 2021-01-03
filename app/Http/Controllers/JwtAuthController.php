<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class JwtAuthController extends Controller {

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request) {
        $req = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required|string|min:5',
        ]);

        if ($req->fails()) {
            return $this->sendError('Error validating.', ['error' => 'Validation ']);
        }

        if (!$token = auth()->attempt($req->validated())) {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }

        return $this->generateToken($token, $request->ip());
    }

    /**
     * Sign up.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {

        $rules = array(
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'phone' => 'required'
        );
        $messages = array(
            'name.required' => 'Please enter a valid name.',
            'email.required' => 'Please enter a valid email .',
            'description.required' => 'your password should be a string of a min 6 characters.',
            'phone.required' => 'Please enter a valid phone number .',
        );
        $req = Validator::make($request->all(), $rules, $messages);

        if ($req->fails()) {
            return $this->sendError('Validation Error.', $req->errors());
        }

        $user = User::create(array_merge(
                                $req->validated(), ['password' => bcrypt($request->password)]
        ));

        return $this->sendResponse($user, 'User register successfully.');
    }

    /**
     * Sign out
     */
    public function signout() {
        auth()->logout();
        return response()->json(['message' => 'User loged out']);
    }

   
    /**
     * Token refresh
     */
    public function refresh() {
        return $this->generateToken(auth()->refresh());
    }

    /**
     * User
     */
    public function searchByUser() {
        return response()->json(auth()->user());
    }

    /**
     * Generate token
     */
    protected function generateToken($token, $ip) {

        $msg['access_token'] = $token;
        $msg['token_type'] = 'bearer';
        $msg['expires_in'] = auth()->factory()->getTTL() * 60;
        $msg['user'] = auth()->user();
        $msg['last_login_at'] = date("Y-m-d h:i:s");
        $msg['last_login_ip'] = $ip;

        return $this->sendResponse($msg, 'User login successfully.');
    }

}
