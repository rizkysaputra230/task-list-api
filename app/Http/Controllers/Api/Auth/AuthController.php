<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuthController extends BaseController
{
    /**
     * Register
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required',
            'email'             => 'required|email',
            'phone'             => 'required|unique:users,phone',
            'password'          => 'required',
            'repeat_password'   => 'required|same:password',
            'upload_photo'      => 'required|mimes:jpg,bmp,png',
            'upload_cv'         => 'required|max:1000|mimes:pdf,doc,docx'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Register failed', $validator->errors());
        }

        $input                  = $request->all();
        $input['password']      = bcrypt($input['password']);

        if ($request->file('upload_photo')) {
            $filePhoto          = $request->file('upload_photo');
            $filenamePhoto      = 'profile' . date('YmdHi') . $filePhoto->getClientOriginalName();
            $filePhoto->move(storage_path('app/public/profile'), $filenamePhoto);
            $input['upload_photo'] = $filenamePhoto;
        }

        if ($request->file('upload_cv')) {
            $file           = $request->file('upload_cv');
            $filename       = 'CV' . date('YmdHi') . $file->getClientOriginalName();
            $file->move(storage_path('app/public/cv'), $filename);
            $input['upload_cv'] = $filename;
        }
        DB::beginTransaction();
        try {
            $user = User::create($input);

            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'User Logged In Successfully',
                'token'     => $user->createToken("API TOKEN")->plainTextToken,
                'user'      => $user->name,
                'photo'     => url('storage/profile/' . $user->upload_photo)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(
                'User register unsuccessfully',
                $e->getMessage()
            );
        }
    }

    /**
     * Login
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Account not found'
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status'    => true,
                'message'   => 'User Logged In Successfully',
                'token'     => $user->createToken("API TOKEN")->plainTextToken,
                'user'      => $user->name,
                'photo'     => url('storage/profile/' . $user->upload_photo)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->sendResponse(
            [],
            'Logout Successful'
        );
    }
}
