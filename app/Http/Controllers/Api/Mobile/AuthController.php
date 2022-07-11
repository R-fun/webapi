<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'email' => 'required|string',
                'password'   => 'required|string',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'message' => $validation->errors()->first(),
                    'serve' => []
                ], 400);
            }

            $credentials = ['email' => $request->get('email'), 'password' => $request->get('password')];

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Email atau password kurang tepat.',
                    'serve' => []
                ], 400);
            }

            $user = Auth::user();
            if (!$user->email_verified_at) {
                Auth::logout();
                return response()->json([
                    'message' => 'Email belum teraktivasi, harap aktivasi terlebih dahulu.',
                    'serve' => []
                ], 400);
            }

            $token = JWTAuth::attempt($request->all());
            return response()->json([
                'message' => '',
                'serve' => [
                    'access_token' => $token,
                    'user' => $user
                ]
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);
            if ($validation->fails()) {
                DB::commit();
                return response()->json([
                    'message' => $validation->errors()->first(),
                    'serve' => []
                ], 400);
            }

            $dataUser = new User;
            $dataUser->email = $request->email;
            $dataUser->name = $request->name;
            $dataUser->password = bcrypt($request->password);
            $dataUser->email_token = base64_encode($request->email);
            $dataUser->role = 1;
            $dataUser->save();

            $dataUser->sendEmailVerificationNotification();
            $success = [
                'message' => "Registrasi akun berhasil, silahkan aktivasi email anda dengan cara klik tautan yang sudah kami kirim melalui email.",
                'serve' => []
            ];
            DB::commit();
            return response()->json($success, 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function forgot(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'serve' => [],
                ], 400);
            }

            $dataUser = User::where('email', $request['email'])->first();
            if (!$dataUser) {
                return response()->json([
                    'message' => "Email belum terdaftar pada sistem.",
                    'serve' => [],
                ], 400);
            }
            $link = Password::sendResetLink($request->only("email"));
            if ($link !== Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => 'Terjadi kesalahan pada server, gagal mengirim tautan silahkan coba kembali.',
                    'serve' => [],
                ], 400);
            }

            return response()->json([
                'message' => "Kami telah mengirimkan tautan pembaruan password ke email Anda, silahkan cek.",
                'serve' => [],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function reset(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'serve' => [],
                ], 400);
            }

            $verify = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );
            if ($verify !== Password::PASSWORD_RESET) {
                return response()->json([
                    'message' => 'Terjadi kesalahan pada server, gagal reset password silahkan coba kembali.',
                    'serve' => [],
                ], 400);
            }

            return response()->json([
                'message' => 'Password berhasil terganti, silahkan coba login kembali.',
                'serve' => [],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens->each(function ($token, $key) {
                $token->delete();
            });
            return response()->json([
                'message' => '',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }
}