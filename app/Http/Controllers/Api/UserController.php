<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\User
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $user = auth('api')->user();
            return response()->json($user);

        }catch(Exception $e){
            return response()->json(["message" => $e->getMessage()], 401);
        }
    }

    public function photo(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'photo' => 'required|image|mimes:jpeg,jpg,png,JPG|max:10240'
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $image = $request->file('photo');

            $model = auth('api')->user();
            $model->uploadImage($image)->save();

            if (!$token = auth('api')->login($model)) {
                throw new Exception('Log out and logi again');
            }

            return response()->json($token);

        }catch(Exception $e){
            return response()->json(["message" => $e->getMessage()], 401);
        }
    }


    public function update(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'phone' => 'required|string|min:9',
                'birthday' => 'required|date',
                'gender' => 'required|string|in:male,female',
                'country' => 'nullable|string|min:6',
                'last_name' => 'required|string',
                'first_name' => 'required|string',
                'address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $model = auth('api')->user();
            $email = $request->input('email');

            if($model->email != $email){
                if(User::where('email', $email)->count() > 0)
                    throw new Exception('Email already taken, Modify or Choose Another');

                $model->email = $email;
            }

            $model->first_name = $request->input('first_name');
            $model->last_name = $request->input('last_name');
            $model->country = $request->input('country', $model->country);
            $model->phone = $request->input('phone');
            $model->gender = $request->input('gender', $model->gender);
            $model->address = $request->input('address', $model->address);
            $model->birthday = date('Y-m-d', strtotime($request->input('birthday', $model->birthday)) );
            $model->updated_at = strtotime("now");

            $model->save();

            if (!$token = auth('api')->login($model)) {
                throw new Exception('Log out and logi again');
            }

            return response()->json($token);

        }catch(Exception $e){
            return response()->json(["message" => $e->getMessage()], 401);
        }
    }
}
