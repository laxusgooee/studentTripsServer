<?php

namespace App\Http\Controllers\Api;


use Exception;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Auth\PasswordBroker;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                throw new Exception('all feilds are required');
            }

            $credentials = $request->only(['email', 'password']);

            if (!$token = auth('api')->attempt($credentials)) {
                throw new Exception('Unauthorized');
            }

            return $this->respondWithToken($token);

        }catch(Exception $e){
            return response()->json(["message" => $e->getMessage()], 401);
        }
    }

    public function register(Request $request)
    {

        try{
            $validator = Validator::make($request->all(), [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:14', 'min:9'],
                'matric_no' => ['required', 'string', 'max:12', 'min:9'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $user = $this->create( $request->only([ 
                'email',  
                'first_name', 
                'last_name',  
                'birthday',  
                'gender',  
                'phone', 
                'matric_no', 
                'password'
            ]));

            if (!$token = auth('api')->login($user)) {
                throw new Exception('Unauthorized');
            }

            return $this->respondWithToken($token);

        }catch(Exception $e){
            return response()->json(["message" => $e->getMessage()], 401);
        }
    }

    public function delete(Request $request)
    {

        try{
            $user = auth('api')->user();

            if(empty($user)){
                throw new Exception('Unauthorized');
            }

            //todo:
            //delete favorites
            //delete notifications
            //delete user

            response()->json("deleted");

        }catch(Exception $e){
            return response()->json(["message" => $e->getMessage()], 401);
        }
    }

    protected function create($data)
    {
        $user = array();

        $user['first_name'] = $data['first_name'];
        $user['last_name'] = $data['last_name'];
        $user['email'] = $data['email'];
        $user['phone'] = $data['phone'];
        $user['password'] = Hash::make( $data['password'] );

        if(!empty($data['gender']))
            $user['gender'] = $data['gender'];

        if(!empty($data['birthday']))
            $user['birthday'] = date('Y-m-d', strtotime($data['birthday']));


        return User::create($user);
    }

    protected function respondWithToken($token)
    {
      return response()->json($token);
    }
}
