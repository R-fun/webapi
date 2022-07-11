<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Toko;
use App\Models\BarangHistory;
use App\Models\BarangToko;
use App\Models\Satuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class TokoController extends Controller
{
    protected function validator(array $data){
        return Validator::make($data,[
            'nama_toko'=>'required|min:1',
            'alamat'=> 'required',
            'user_id'=> 'required',
            'jenis_toko'=> 'required'
        ]);
    }
    /**
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/toko",
      *     tags={"Projects UMKM"},
      *     summary="Looking Data Toko",
      *     description="Looking Data Toko",
      *     operationId="getToko",
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
            $dataSatuan = Toko::with('User')->when($name, function ($query) use ($name) {
                return $query->where('nama_toko', 'like', '%' . $name . '%');
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
      *     path="/toko/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Get By Id Data Toko Admin",
      *     description="Get By Id Data Toko Admin",
      *     operationId="getByIdToko",
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
            if(!Toko::find($id)){
                return response()->json([
                    'status'=>false,
                    'message'=>'Data Tidak Ditemukan'
                ],404);
            }
            $toko = Toko::with('User')->where('id',$id)->first();

            return response()->json([
                'status'=>true,
                'toko' => $toko
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
      *     path="/toko",
      *     tags={"Projects UMKM"},
      *     summary="Insert Data Toko Admin",
      *     description="Insert Data Toko Admin",
      *     operationId="insertToko",
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *               type="object",
      *               @OA\Property(property="nama_toko"),
      *               @OA\Property(property="alamat"),
      *               @OA\Property(property="user_id"),
      *               @OA\Property(property="jenis_toko"),
      *
      *     ),
      *         @OA\Schema(
      *               type="object",
      *               @OA\Property(property="nama_toko", type="text"),
      *               @OA\Property(property="alamat", type="text"),
      *               @OA\Property(property="user_id", type="text"),
      *               @OA\Property(property="jenis_toko", type="text"),
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

    public function save(Request $request){
        DB::beginTransaction();
        try{
            $validate = Validator::make($request->all(),[
                'nama_toko'=>'required|min:1',
                'alamat'=> 'required',
                'user_id'=> 'required',
                'jenis_toko'=> 'required'
            ]);

            if($validate->fails()){
                DB::commit();
                return response()->json([
                    'status'=>false,
                    'message'=>$validate->errors()->first()
                ],400);
            }

            Toko::create([
                'nama_toko'=>$request->nama_toko,
                'alamat'=>$request->alamat,
                'user_id'=>$request->user_id,
                'jenis_toko'=>$request->jenis_toko
            ]);
            DB::commit();
            return response()->json([
                'status'=>true,
                'message'=>'Tambah Toko Berhasil'
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
      *     path="/toko/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Update Data Toko Admin",
      *     description="Update Data Toko Admin",
      *     operationId="updateToko",
      *     @OA\Parameter(
      *         name="id",
      *         in="path",
      *         description="Enter Id Data For Update",
      *         required=true,
      *     ),
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *               type="object",
      *               @OA\Property(property="nama_toko"),
      *               @OA\Property(property="alamat"),
      *               @OA\Property(property="user_id"),
      *               @OA\Property(property="jenis_toko"),
      *
      *     ),
      *         @OA\Schema(
      *               type="object",
      *               @OA\Property(property="nama_toko", type="text"),
      *               @OA\Property(property="alamat", type="text"),
      *               @OA\Property(property="user_id", type="text"),
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
            if(!Toko::find($id)){
                return response()->json([
                    'status'=>false,
                    'message'=>'Data Tidak Ditemukan'
                ],404);
            }
            $validate = Validator::make($request->all(),[
                'nama_toko'=>'required|min:1',
                'alamat'=> 'required',
                'user_id'=> 'required',
                'jenis_toko'=> 'required'
            ]);

            if($validate->fails()){
                DB::commit();
                return response()->json([
                    'status'=>false,
                    'message'=>$validate->errors()->first()
                ],400);
            }

            Toko::where('id',$id)->update([
                'nama_toko'=>$request->nama_toko,
                'alamat'=>$request->alamat,
                'user_id'=>$request->user_id,
                'jenis_toko'=>$request->jenis_toko
            ]);
            DB::commit();
            return response()->json([
                'status'=>true,
                'message'=>'Edit Toko Berhasil'
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
      *     path="/toko/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Delete Data Toko Admin",
      *     description="Delete Data Toko Admin",
      *     operationId="deleteToko",
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
            if(!Toko::find($id)){
                return response()->json([
                    'status'=>false,
                    'message'=>'Data Tidak Ditemukan'
                ],404);
            }
            Toko::where('id',$id)->delete();
            DB::commit();
            return response()->json([
                'status'=>true,
                'message'=>'Hapus Toko Berhasil'
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
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/count/toko",
      *     tags={"Projects UMKM"},
      *     summary="Count Data Toko",
      *     description="Count Data Toko",
      *     operationId="countToko",
      *
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     )
      *)
      *
    */

    public function getCountToko(){
        try{
            $toko = Toko::all();
            $count = count($toko);
            return response()->json([
                'status' => true,
                'count' => $count
            ]);
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
      *     path="/order/toko",
      *     tags={"Projects UMKM"},
      *     summary="Order Descending Data Toko",
      *     description="Order Descending Data Toko",
      *     operationId="orderToko",
      *
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     )
      *)
      *
    */

    public function orderToko(){
        try{
            $toko = Toko::whereDate('created_at',Carbon::today())->orderBy('created_at','DESC')->offset(0)->limit(5)->get();
            return response()->json([
                'status'=>true,
                'toko'=>$toko
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
      *     path="/toko/barang",
      *     tags={"Projects UMKM"},
      *     summary="Get Data Barang Toko Admin",
      *     description="Get Data Barang Toko Admin",
      *     operationId="getDataBarangByToko",
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *               type="object",
      *               @OA\Property(property="toko"),
      *
      *     ),
      *         @OA\Schema(
      *               type="object",
      *               @OA\Property(property="toko", type="text"),
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

    public function getBarang(Request $request){
        try{
            $barang = BarangToko::with(['Barang','Toko'])->where('toko_id',$request->toko)->get();
            return response()->json([
                'status'=>true,
                'data' => $barang,
            ],200);
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
          *     path="/filter/toko",
          *     tags={"Projects UMKM"},
          *     summary="Filter Data Toko By Jenis Toko",
          *     description="Filter Data Toko By Jenis Toko",
          *     operationId="filterToko",
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

        public function filtertoko(Request $request){
            try{
                $name = $request->query('name');
                $size = $request->pageSize ? $request->pageSize:10;
                $toko = Toko::with('User')->when($name, function ($query) use ($name) {
                    return $query->where('nama_toko', 'like', '%' . $name . '%');
                })->where('jenis_toko',$request->jenis_toko)->orderBy("created_at", "desc")->paginate($size);
                // $toko = Toko::with('User')->where('jenis_toko',$request->jenis_toko)->get();
                return response()->json([
                    'status'=>true,
                    'data'=>$toko
                ]);
            }catch (Throwable $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'serve' => [],
                ], 500);
            }
        }
}
