<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Validator;
class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth:api', ['except'=>['login','register']]);
    }
    public function index()
    {
        $users = User::where('id', '>', 0)->get();
        return response()->json($users);
    }

    public function register(Request $request)
    {
        if($request->type == 1) {
            $validator = Validator::make($request->all(),[
                    'email' => ['required','email', 'unique:users'],
                    'password' => ['required'],
                    'name' => [],
                ]);
    
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 400);
            }

            $newUser = new User;
            $newUser->email = $request->email;
            $newUser->name = $request->name;
            $newUser->password = Hash::make($request->password);
            $newUser->save();
            return response()->json(['data'=>$newUser], 200);
        }
        else if($request->type == 2){
        $validator = Validator::make($request->all(),[
                'email' => ['required','email', 'unique:users'],
                'password' => [],
                'name' => [],
                'thirdAppId' =>['required']
            ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
        return response()->json(['type'=>"register"]);
        }else if($request->type == 3){
            if(!$user){
                $newUser = new User;
                $newUser->email = $request->email;
                $newUser->name = $request->name;
                $newUser->google_id = $request->thirdAppId;
                $newUser->save();
                return response()->json(['type'=>'login', 'data'=>$newUser], 200);
            }else if($user->email == $request->email && $user->google_id == $request->thirdAppId){
                return response()->json(['type'=>"login"]);
            }
        }
    }
    public function login(Request $request)
    {
        try {
            if($request->type == 1){
                $validator = Validator::make($request->all(),[
                    'email' => ['required','email'],
                    'password' => ['required'],
                ]);

                if($validator->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => $validator->errors()
                    ], 400);
                }

                $loginValue = $request->only('email', 'password');
                $token = Auth::attempt($loginValue);
                if(!$token){
                    return response()->json([
                        'status' => false,
                        'message' => "Email or password invalid!"
                    ], 400);
                }

                $user = Auth::user();
                return response()->json([
                'data'=>$user, 
                'authorization'=>[
                    'token'=>$token,
                    'type'=>'Bearer'
                ],
                ],                
                200);
            }
            else if($request->type == 2){
                $validator = Validator::make($request->all(),[
                    'email' => ['required','email'],
                    'password' => [],
                    'name' => [],
                ]);
    
                if($validator->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => $validator->errors()
                    ], 400);
                }

                $user = User::where('email', '=', $request->email)->first();
                if(!$user){
                    return response()->json(['type'=>'register']);
                }else if($user && $request->thirdAppId != $user->google_id){
                    return response()->json(['email'=>'The email has already been taken.']);
                }
                return response()->json(['type'=>'login']);
            }
            // return response()->json(['type'=>'login']);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function me(Request $request)
    {

    }

}
