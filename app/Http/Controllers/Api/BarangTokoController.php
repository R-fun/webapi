<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangHistory;
use App\Models\BarangToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class BarangTokoController extends Controller
{
    protected function validator(array $data){
        return Validator::make($data,[
            'barang_id'=>'required|numeric',
            'toko_id'=>'required|numeric'
        ]);
    }

    /**
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/barangtoko",
      *     tags={"Projects UMKM"},
      *     summary="Looking Data Barang And Toko",
      *     description="Looking Data Barang And Toko",
      *     operationId="getBarangToko",
      *
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     )
      *)
      *
        */

        public function get(Request $request){
            try{
                $name = $request->query('name');
                $size = $request->pageSize ? $request->pageSize:10;
                $barangtoko = BarangToko::when($name,function ($query) use ($name){
                    return $query->where('nama_barang','like','%'.$name.'%')->join('barang','barang_toko.barang_id','=','barang.id');
                })->orderBy('barang_toko.created_at','desc')->select('barang_toko.*')->paginate($size);
                return response()->json([
                    'status'=>true,
                    'data' => $barangtoko,
                ],200);
            }catch (Throwable $e) {
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
      *     path="/barangtoko/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Get By Id Data Barang And Toko",
      *     description="Get By Id Data Barang And Toko",
      *     operationId="getByIdBarangToko",
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
                if(!BarangToko::find($id)){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Data Tidak Ditemukan'
                    ],404);
                }

                $dataBarangToko = BarangToko::where('id',$id)->get();

                return response()->json([
                    'status'=>true,
                    'data'=> $dataBarangToko,
                ]);

            }catch (Throwable $e) {
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
          *     path="/barangtoko",
          *     tags={"Projects UMKM"},
          *     summary="Insert Data BarangToko Admin",
          *     description="Insert Data BarangToko Admin",
          *     operationId="insertBarangToko",
          *     @OA\RequestBody(
          *         @OA\JsonContent(
          *               type="object",
          *               @OA\Property(property="barang_id"),
          *               @OA\Property(property="toko_id"),
          *
          *     ),
          *         @OA\Schema(
          *               type="object",
          *               @OA\Property(property="barang_id", type="text"),
          *               @OA\Property(property="toko_id", type="text"),
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

                BarangToko::create([
                    'barang_id'=>$request->barang_id,
                    'toko_id'=>$request->toko_id,
                ]);

                BarangHistory::create([
                    'barang_id'=>$request->barang_id,
                    'harga_barang'=>'0',
                    'toko_id'=>$request->toko_id,
                    'operation'=>'INSERT',
                    'user_id'=>$request->user()->id,
                ]);

                DB::commit();
                return response()->json([
                    'status'=>true,
                    'message'=>'Tambah BarangToko Berhasil'
                ]);
            }catch(Throwable $e){
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
          *     path="/barangtoko/{id}",
          *     tags={"Projects UMKM"},
          *     summary="Update Data BarangToko Admin",
          *     description="Update Data BarangToko Admin",
          *     operationId="updateBarangToko",
          *     @OA\Parameter(
          *         name="id",
          *         in="path",
          *         description="Enter Id Data For Update",
          *         required=true,
          *     ),
          *     @OA\RequestBody(
          *         @OA\JsonContent(
          *               type="object",
          *               @OA\Property(property="barang_id"),
          *               @OA\Property(property="toko_id"),
          *
          *     ),
          *         @OA\Schema(
          *               type="object",
          *               @OA\Property(property="barang_id", type="text"),
          *               @OA\Property(property="toko_id", type="text"),
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
                $validate = $this->validator($request->all());
                if($validate->fails()){
                    DB::commit();
                    return response()->json([
                        'status'=>false,
                        'message'=>$validate->errors()->first()
                    ],400);
                }

                if(!BarangToko::find($id)){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Data Tidak Ditemukan'
                    ],404);
                }

                BarangToko::where('id',$id)->update([
                    'barang_id'=>$request->barang_id,
                    'toko_id'=>$request->toko_id,
                ]);

                $barangtoko = BarangHistory::where('barang_id',$request->barang_id)->where('toko_id',$request->toko_id)->orderBy('created_at','DESC')->first();
                BarangHistory::create([
                    'barang_id'=>$request->barang_id,
                    'harga_barang'=>'0',
                    'toko_id'=>$request->toko_id,
                    'operation'=>'UPDATE',
                    'user_id'=>$request->user()->id,
                ]);

                DB::commit();
                return response()->json([
                    'status'=>true,
                    'message'=>'Edit BarangToko Berhasil'
                ]);
            }catch(Throwable $e){
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
          *     path="/barangtoko/{id}",
          *     tags={"Projects UMKM"},
          *     summary="Delete Data BarangToko Admin",
          *     description="Delete Data BarangToko Admin",
          *     operationId="deleteBarangToko",
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
        public function erase($id,Request $request){
            DB::beginTransaction();
            try{
                if(!BarangToko::find($id)){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Data Tidak Ditemukan'
                    ],404);
                }
                BarangToko::where('id',$id)->delete();
                DB::commit();
                //BarangHistory::create([
                //   'barang_id'=>$id,
                //    'operation'=>'DELETE',
                //    'keterangan'=>'Menghapus Data Barang',
                //    'user_id'=>$request->user()->id,
                //]);

                return response()->json([
                    'status'=>true,
                    'message'=>'Hapus BarangToko Berhasil'
                ]);
            }catch(Throwable $e){
                DB::rollBack();
                return response()->json([
                    'message' => $e->getMessage(),
                    'serve' => [],
            ], 500);
            }
        }
}
