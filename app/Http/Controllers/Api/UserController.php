<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangHistory;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UserController extends Controller
{
    /**
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/users",
      *     tags={"Projects UMKM"},
      *     summary="Looking Account Admin",
      *     description="Looking For Account Admin Login",
      *     operationId="users",
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     ),
      *)
      *
    */

    public function getUser(Request $request){
        try {
            $name = $request->query('name');
            $size = $request->pageSize ? $request->pageSize:10;
            $dataSatuan = User::when($name, function ($query) use ($name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })->where('role',1)->orderBy("created_at", "desc")->paginate($size);
            return response()->json([
                'message' => '',
                'serve' => $dataSatuan,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

            /**
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/user/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Get By Id Data User",
      *     description="Get By Id Data User",
      *     operationId="getByIdUser",
      *     @OA\Parameter(
      *         name="id",
      *         in="path",
      *         description="Enter Id Data For Get Data And Relation",
      *         required=true,
      *     ),
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     )
      *)
      *
        */

    public function getById($id){
        try{
            if(!User::find($id)){
                return response()->json([
                    'status'=>false,
                    'message'=>'Data Tidak Ditemukan'
                ],404);
            }
            $user = User::where('id',$id)->first();
            return response()->json([
                'status'=>true,
                'data'=>$user
            ]);
        }catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    /**
          * @OA\Delete(
          *      security={{
          *        "bearerAuth":{}
          *      }},
          *     path="/user/{id}",
          *     tags={"Projects UMKM"},
          *     summary="Delete Data User Admin",
          *     description="Delete Data User Admin",
          *     operationId="deleteUser",
          *     @OA\Parameter(
          *         name="id",
          *         in="path",
          *         description="Enter Id Data For Delete",
          *         required=true,
          *     ),
          *     @OA\Response(
          *         response="default",
          *         description="successful operation"
          *     )
          *)
          *
        */

    public function erase($id){
        DB::beginTransaction();
        try{
            if(!User::find($id)){
                return response()->json([
                    'status'=>false,
                    'message'=>'Data Tidak Ditemukan'
                ],404);
            }
            User::where('id',$id)->delete();
            DB::commit();
            return response()->json([
                'status'=>true,
                'message'=>'Hapus User Berhasil'
            ]);
        }catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    /**
      * @OA\Put(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/users",
      *     tags={"Projects UMKM"},
      *     summary="update profile admin",
      *     description="update profile Admin",
      *     operationId="updateprofile",
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *               type="object",
      *               @OA\Property(property="user_id"),
      *               @OA\Property(property="email"),
      *
      *     ),
      *         @OA\Schema(
      *               type="object",
      *               @OA\Property(property="user_id", type="text"),
      *               @OA\Property(property="email", type="text"),
      *
      *
      *         ),
      *     ),
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     ),
      *)
      *
        */

        public function updateprofile(Request $request){
            DB::beginTransaction();
            try{
                $validate = Validator::make($request->all(),[
                    'email'=>'required|min:1|email',
                    'user_id'=> 'required',
                ]);

                if($validate->fails()){
                    DB::commit();
                    return response()->json([
                        'status'=>false,
                        'message'=>$validate->errors()->first()
                    ],400);
                }

                User::where('id',$request->user_id)->update([
                    'email'=>$request->email,
                ]);
                DB::commit();
                return response()->json([
                    'status'=>true,
                    'message'=>'Edit Profile Berhasil'
                ]);
            }catch (Throwable $e) {
                DB::rollBack();
                return response()->json([
                    'message' => $e->getMessage(),
                    'serve' => [],
                ], 500);
            }
        }


        /**
      * @OA\Put(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/user/password",
      *     tags={"Projects UMKM"},
      *     summary="update password user",
      *     description="update password user",
      *     operationId="updatepassword",
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *               type="object",
      *               @OA\Property(property="user_id"),
      *               @OA\Property(property="password_baru"),
      *
      *     ),
      *         @OA\Schema(
      *               type="object",
      *               @OA\Property(property="user_id", type="text"),
      *               @OA\Property(property="password_baru", type="text"),
      *
      *
      *         ),
      *     ),
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     ),
      *)
      *
        */

        public function updatepassword(Request $request){
            DB::beginTransaction();
            try{
                $validate = Validator::make($request->all(),[
                    'password_baru'=>'required|min:8',
                    'user_id'=> 'required',
                ]);

                if($validate->fails()){
                    DB::commit();
                    return response()->json([
                        'status'=>false,
                        'message'=>$validate->errors()->first()
                    ],400);
                }

                $user = User::where('id',$request->user_id)->first();
                User::where('id',$request->user_id)->update([
                    'password'=>bcrypt($request->password_baru),
                ]);

                DB::commit();
                return response()->json([
                    'status'=>true,
                    'message'=>'Ubah Password Berhasil'
                ]);
            }catch (Throwable $e) {
                DB::rollBack();
                return response()->json([
                    'message' => $e->getMessage(),
                    'serve' => [],
                ], 500);
            }
        }


          /**
      * @OA\Put(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/admin/password",
      *     tags={"Projects UMKM"},
      *     summary="update password admin",
      *     description="update password Admin",
      *     operationId="updatepasswordAdmin",
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *               type="object",
      *               @OA\Property(property="user_id"),
      *               @OA\Property(property="password_lama"),
      *               @OA\Property(property="password_baru"),
      *
      *     ),
      *         @OA\Schema(
      *               type="object",
      *               @OA\Property(property="user_id", type="text"),
      *               @OA\Property(property="password_lama", type="text"),
      *               @OA\Property(property="password_baru", type="text"),
      *
      *
      *         ),
      *     ),
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     ),
      *)
      *
        */

        public function updatepasswordAdmin(Request $request){
            DB::beginTransaction();
            try{
                $validate = Validator::make($request->all(),[
                    'password_baru'=>'required|min:8',
                    'password_lama'=>'required|min:8',
                    'user_id'=> 'required',
                ]);

                if($validate->fails()){
                    DB::commit();
                    return response()->json([
                        'status'=>false,
                        'message'=>$validate->errors()->first()
                    ],400);
                }


                $user = User::where('id',$request->user_id)->first();

                if(!Hash::check($request->password_lama,$user->password)){
                    DB::commit();
                    return response()->json([
                        'status'=>false,
                        'message'=>'Password Lama Salah'
                    ],400);
                }

                User::where('id',$request->user_id)->update([
                    'password'=>bcrypt($request->password_baru),
                ]);

                DB::commit();
                return response()->json([
                    'status'=>true,
                    'message'=>'Ubah Password Berhasil'
                ]);
            }catch (Throwable $e) {
                DB::rollBack();
                return response()->json([
                    'message' => $e->getMessage(),
                    'serve' => [],
                ], 500);
            }
        }
}
