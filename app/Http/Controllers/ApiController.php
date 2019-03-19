<?php

namespace App\Http\Controllers;
use App\Http\Requests\RegisterAuthRequest;
use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class ApiController extends Controller
{
    public $loginAfterSignUp = true;
    public function register(RegisterAuthRequest $request)
    {
        /*
        * Make a new user to register and save the data.
        */
        $user = new User();
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=bcrypt($request->password);
        $user->mobile_number=$request->mobile_number;
        $user->save();
        /*
        * Check if Logged in.
        */
        if($this->loginAfterSignUp)
        {
            return $this->login($request);
        }
        return response()->json([
            'success'=>true,
            'data'=>$user
        ],200);
    }
    public function login(Request $request)
    {
        $loggedInData=$request->only('email','password');
        $jwt_token=null;
        /*
        * check for the given token
        */
        if(!$jwt_token=JWTAuth::attempt($loggedInData))
        {
            return response()->json([
                'success'=>false,
                'message'=>'Invalid Email or Password',
            ],401);
        }
        return response()->json([
            'success'=>true,
            'token'=>$jwt_token
        ]);
    }
    public function logout(Request $request)
    {
        $this->validate($request,[
            'token'=>'required'
        ]);
        /*
        *the request is validated that it contains the token field.
        *
        */
        try
        {
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success'=>true,
                'message'=>'User logged out successfully'
            ]);
        }
        /*
        * If the JWTException exception caught, a failure response is returned.
        */
        catch(JWTException $exception)
        {
            return response()->json([
                'success'=>false,
                'message'=>'Sorry, the user cannot be logged out'
            ],500);
        }
    }
    public function getAuthUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
        $user = JWTAuth::authenticate($request->token);
        return response()->json(['user' => $user]);
    }
}
