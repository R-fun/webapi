<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Throwable;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
      * @OA\Post(
      *     path="/login",
      *     tags={"Projects UMKM"},
      *     summary="Login Admin,for admin i guess",
      *     description="Login Admin,for admin i guess",
      *     operationId="login",
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *               type="object",
      *               @OA\Property(property="email"),
      *               @OA\Property(property="password"),
      *     ),
      *         @OA\Schema(
      *               type="object",
      *               @OA\Property(property="email", type="text"),
      *               @OA\Property(property="password", type="password"),
      *
      *         ),
      *     ),
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     )
      *)
      *
        */

    public function __invoke(Request $request)
    {
        try{
            $validate = Validator::make($request->all(),[
                'email'=>'email|required',
                'password'=>'required|min:8'
            ]);

            if($validate->fails()){
                DB::commit();
                return response()->json([
                    'status'=>false,
                    'message'=>$validate->errors()->first()
                ],400);
            }

            if($token = JWTAuth::attempt($request->all())){
                return response()->json([
                    'token'=> $token,
                    'status'=>true,
                    'user'=>$request->user(),
                    'message'=>'Login Success',
                ]);
            }else{
                return response()->json([
                    'status'=>false,
                    'message'=>'Kesalahan Email Atau Password'
                ]);
            }
        }catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }
}
