<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    /**
     * Register
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required',
            'email'             => 'required|email',
            'password'          => 'required',
            'repeat_password'   => 'required|same:password',
            'upload_photo'      => 'required|mimes:jpg,bmp,png',
            'upload_cv'         => 'required|max:1000|mimes:pdf,doc,docx'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input                  = $request->all();
        $input['password']      = bcrypt($input['password']);

        if ($request->file('upload_photo')) {
            $file           = $request->file('upload_photo');
            $filename       = 'photo' . date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('public/profile'), $filename);
            $input['upload_cv'] = $filename;
        }

        if ($request->file('upload_cv')) {
            $file           = $request->file('upload_cv');
            $filename       = 'CV' . date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('public/cv'), $filename);
            $input['upload_cv'] = $filename;
        }

        try {
            $user = User::create($input);
            $success['token']       =  $user->createToken('MyApp')->plainTextToken;
            $success['name']        =  $user->name;
            $link                   = '/dashboard';
            return $this->sendResponse(
                $success,
                'User register successfully.',
                $link
            );
        } catch (\Exception $e) {
            return $this->sendError(
                $e->getMessage(),
                'User register unsuccessfully'
            );
        }
    }

    /**
     * Login
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token']       =  $user->createToken('MyApp')->plainTextToken;
            $success['name']        =  $user->name;
            $link                   = '/dashboard';
            return $this->sendResponse(
                $success,
                'User login successfully.',
                $link
            );
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }
}
