<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
      * @OA\Post(
      *     path="/register",
      *     tags={"Projects UMKM"},
      *     summary="Register Admin,for admin i guess",
      *     description="Register Admin,for admin i guess",
      *     operationId="register",
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *               type="object",
      *               @OA\Property(property="name"),
      *               @OA\Property(property="email"),
      *               @OA\Property(property="password"),
      *               @OA\Property(property="role"),
      *     ),
      *         @OA\Schema(
      *               type="object",
      *               @OA\Property(property="name", type="text"),
      *               @OA\Property(property="email", type="text"),
      *               @OA\Property(property="password", type="password"),
      *               @OA\Property(property="role", type="integer"),
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
        DB::beginTransaction();
        try{
            $validate = Validator::make($request->all(),[
                'name'=>'required',
                'email'=>'email|required',
                'password'=>'required|min:8',
                'role'=>'required'
            ]);
            if($validate->fails()){
                DB::commit();
                return response()->json([
                    'status'=>false,
                    'message'=>$validate->errors()->first()
                ],400);
            }

            User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>bcrypt($request->password),
                'role'=>$request->role
            ]);
            DB::commit();
            return response()->json([
                'status'=>true,
                'message'=>'Register Success',
            ],201);
        }catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }
}
