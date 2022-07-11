<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisToko;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class JenisTokoController extends Controller
{
    protected function validator(array $data){
        return Validator::make($data,[
            'jenis_toko'=>'required',
        ]);
    }

    /**
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/jenistoko",
      *     tags={"Projects UMKM"},
      *     summary="Looking Data JenisToko",
      *     description="Looking Data JenisToko",
      *     operationId="getjenisToko",
      *
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     )
      *)
      *
    */

    public function get(Request $request){
        try {
            $name = $request->query('name');
            $size = $request->pageSize ? $request->pageSize:10;
            $dataSatuan = JenisToko::when($name, function ($query) use ($name) {
                return $query->where('jenis_toko', 'like', '%' . $name . '%');
            })->orderBy("created_at", "desc")->paginate($size);
            return response()->json([
                'message' => '',
                'data' => $dataSatuan,
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
      *     path="/jenistoko/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Get By Id Data JenisToko Admin",
      *     description="Get By Id Data JenisToko Admin",
      *     operationId="getByIdjenisToko",
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
            if(!JenisToko::find($id)){
                return response()->json([
                    'status'=>false,
                    'message'=>'Data Tidak Ditemukan'
                ],404);
            }

            $data= JenisToko::where('id',$id)->get();

            return response()->json([
                'status'=>true,
                'data'=>$data
            ]);
        }catch(Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    /**
          * @OA\Post(
          *      security={{
          *        "bearerAuth":{}
          *      }},
          *     path="/jenistoko",
          *     tags={"Projects UMKM"},
          *     summary="Insert Data JenisToko Admin",
          *     description="Insert Data JenisToko Admin",
          *     operationId="insertJenisToko",
          *     @OA\RequestBody(
          *         @OA\JsonContent(
          *               type="object",
          *               @OA\Property(property="jenis_toko"),
          *
          *     ),
          *         @OA\Schema(
          *               type="object",
          *               @OA\Property(property="jenis_toko", type="text"),
          *
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

    public function save(Request $request){
        DB::beginTransaction();
        try{
            $validate = $this->validator($request->all());
            if($validate->fails()){
                DB::commit();
                return response()->json([
                    'status'=>false,
                    'message'=>$validate->errors()->first()
                ],400);
            }

            JenisToko::create([
                'jenis_toko'=>$request->jenis_toko,
            ]);
            DB::commit();
            return response()->json([
                'status'=>true,
                'message'=>'Tambah JenisToko Berhasil'
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
      *     path="/jenistoko/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Update Data JenisToko Admin",
      *     description="Update Data JenisToko Admin",
      *     operationId="updateJenisToko",
      *     @OA\Parameter(
      *         name="id",
      *         in="path",
      *         description="Enter Id Data For Update",
      *         required=true,
      *     ),
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *               type="object",
      *               @OA\Property(property="jenis_toko"),
      *
      *     ),
      *         @OA\Schema(
      *               type="object",
      *               @OA\Property(property="jenis_toko", type="text"),
      *
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

    public function update($id,Request $request){
        DB::beginTransaction();
        try{
            if(!JenisToko::find($id)){
                return response()->json([
                    'status'=>false,
                    'message'=>'Data Tidak Ditemukan'
                ],404);
            }
            $validate = $this->validator($request->all());

            if($validate->fails()){
                DB::commit();
                return response()->json([
                    'status'=>false,
                    'message'=>$validate->errors()->first()
                ],400);
            }

            JenisToko::where('id',$id)->update([
                'jenis_toko'=>$request->jenis_toko
            ]);
            DB::commit();
            return response()->json([
                'status'=>true,
                'message'=>'Edit JenisToko Berhasil'
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
      * @OA\Delete(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/jenistoko/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Delete Data JenisToko Admin",
      *     description="Delete Data JenisToko Admin",
      *     operationId="deleteJenisToko",
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
            if(!JenisToko::find($id)){
                return response()->json([
                    'status'=>false,
                    'message'=>'Data Tidak Ditemukan'
                ],404);
            }
            JenisToko::where('id',$id)->delete();
            DB::commit();
            return response()->json([
                'status'=>true,
                'message'=>'Hapus JenisToko Berhasil'
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
