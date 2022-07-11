<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangHistory;
use App\Models\Satuan;
use App\Models\Toko;
use App\Models\BarangToko;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class BarangController extends Controller
{

    protected function validator(array $data){
        return Validator::make($data,[
            'nama_barang'=>'required|min:1',
            'category'=>'required'
        ]);
    }

    /**
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/barang",
      *     tags={"Projects UMKM"},
      *     summary="Looking Data Barang",
      *     description="Looking Data Barang",
      *     operationId="getBarang",
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
                $barang = Barang::when($name,function ($query) use ($name){
                    return $query->where('nama_barang','like','%'.$name.'%');
                })->orderBy('created_at','desc')->paginate($size);
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
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/barang/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Get By Id Data Barang",
      *     description="Get By Id Data Barang",
      *     operationId="getByIdBarang",
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
                if(!Barang::find($id)){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Data Tidak Ditemukan'
                    ],404);
                }

                $dataBarang = Barang::where('id',$id)->get();

                return response()->json([
                    'status'=>true,
                    'barang'=> $dataBarang,
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
          *     path="/barang",
          *     tags={"Projects UMKM"},
          *     summary="Insert Data Barang Admin",
          *     description="Insert Data Barang Admin",
          *     operationId="insertBarang",
          *     @OA\RequestBody(
          *         @OA\JsonContent(
          *               type="object",
          *               @OA\Property(property="nama_barang"),
          *               @OA\Property(property="merk"),
          *               @OA\Property(property="category"),
          *
          *     ),
          *         @OA\Schema(
          *               type="object",
          *               @OA\Property(property="nama_barang", type="text"),
          *               @OA\Property(property="merk", type="text"),
          *               @OA\Property(property="category", type="text"),
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

                $pattern = Barang::where('nama_barang','like','%'.$request->nama_barang.'%')->first();
                if($pattern){
                    DB::commit();
                    return response()->json([
                        'status'=>false,
                        'message'=>'Nama Barang Sudah Ada'
                    ],400);
                }

                $barang = Barang::create([
                    'nama_barang'=>$request->nama_barang,
                    'merk'=>$request->merk,
                    'category'=>$request->category,
                ]);

                // BarangHistory::create([
                //     'barang_id'=>$barang->id,
                //     'harga_barang'=>'0',
                //     'satuan'=>1,
                //     'operation'=>'INSERT',
                //     'user_id'=>$request->user()->id,
                // ]);
                DB::commit();
                return response()->json([
                    'status'=>true,
                    'message'=>'Tambah Barang Berhasil'
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
          *     path="/barang/{id}",
          *     tags={"Projects UMKM"},
          *     summary="Update Data Barang Admin",
          *     description="Update Data Barang Admin",
          *     operationId="updateBarang",
          *     @OA\Parameter(
          *         name="id",
          *         in="path",
          *         description="Enter Id Data For Update",
          *         required=true,
          *     ),
          *     @OA\RequestBody(
          *         @OA\JsonContent(
          *               type="object",
          *               @OA\Property(property="nama_barang"),
          *               @OA\Property(property="merk"),
          *               @OA\Property(property="category"),
          *
          *     ),
          *         @OA\Schema(
          *               type="object",
          *               @OA\Property(property="nama_barang", type="text"),
          *               @OA\Property(property="merk", type="text"),
          *               @OA\Property(property="category", type="text"),
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

                if(!Barang::find($id)){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Data Tidak Ditemukan'
                    ],404);
                }

                // $barang = Barang::find($id);
                $baranghistory = BarangHistory::where('barang_id',$id)->orderBy('created_at','desc')->orderBy('updated_at')->first();
                Barang::where('id',$id)->update([
                    'nama_barang'=>$request->nama_barang,
                    'merk'=>$request->merk,
                    'category'=>$request->category
                ]);

                // BarangHistory::create([
                //     'barang_id'=>$id,
                //     'harga_barang'=>$baranghistory->harga_barang,
                //     'satuan'=>1,
                //     'operation'=>'UPDATE',
                //     'user_id'=>$request->user()->id,
                // ]);
                DB::commit();
                return response()->json([
                    'status'=>true,
                    'message'=>'Edit Barang Berhasil'
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
          *     path="/barang/{id}",
          *     tags={"Projects UMKM"},
          *     summary="Delete Data Barang Admin",
          *     description="Delete Data Barang Admin",
          *     operationId="deleteBarang",
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
                if(!Barang::find($id)){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Data Tidak Ditemukan'
                    ],404);
                }
                Barang::where('id',$id)->delete();
                DB::commit();
                //BarangHistory::create([
                //   'barang_id'=>$id,
                //    'operation'=>'DELETE',
                //    'keterangan'=>'Menghapus Data Barang',
                //    'user_id'=>$request->user()->id,
                //]);

                return response()->json([
                    'status'=>true,
                    'message'=>'Hapus Barang Berhasil'
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
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/count/barang",
      *     tags={"Projects UMKM"},
      *     summary="Count Data Barang",
      *     description="Count Data Barang",
      *     operationId="countBarang",
      *
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     )
      *)
      *
    */

    public function getCountBarang(){
        try{
            $barang = Barang::all();
            $count = count($barang);
            return response()->json([
                'status'=>true,
                'count'=>$count
            ]);
        }catch(Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    // /**
    //   * @OA\Get(
    //   *      security={{
    //   *        "bearerAuth":{}
    //   *      }},
    //   *     path="/count/toko/{id}",
    //   *     tags={"Projects UMKM"},
    //   *     summary="Get By Id Toko Data  Barang",
    //   *     description="Get By Id Toko Data Barang",
    //   *     operationId="getByIdTokoBarang",
    //   *     @OA\Parameter(
    //   *         name="id",
    //   *         in="path",
    //   *         description="Enter Id Data For Get Data Barang By Id Toko",
    //   *         required=true,
    //   *     ),
    //   *     @OA\Response(
    //   *         response="default",
    //   *         description="successful operation"
    //   *     )
    //   *)
    //   *
    //     */

    // public function getCountByToko($id){
    //     try{
    //         if(!Toko::find($id)){
    //             return response()->json([
    //                 'status'=>false,
    //                 'message'=>'Data Tidak Ditemukan'
    //             ],404);
    //         }
    //         $barang = Barang::where('toko',$id)->get();
    //         $count = count($barang);
    //         return response()->json([
    //             'status'=>true,
    //             'count'=>$count
    //         ]);
    //     }catch(Throwable $e){
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //             'serve' => [],
    //         ], 500);
    //     }
    // }

      /**
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/order/barang",
      *     tags={"Projects UMKM"},
      *     summary="Order By Descending Data Barang",
      *     description="Order By Descending Data Barang",
      *     operationId="orderBarang",
      *
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     )
      *)
      *
    */

    public function orderBarang(){
        try{
            $barang = Barang::whereDate('created_at',Carbon::today())->orderBy('created_at','DESC')->offset(0)->limit(5)->get();
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

    // /**
    //       * @OA\Post(
    //       *      security={{
    //       *        "bearerAuth":{}
    //       *      }},
    //       *     path="/barang/updateharga",
    //       *     tags={"Projects UMKM"},
    //       *     summary="Uodate Data Harga Barang Admin",
    //       *     description="Update Data Harga Barang Admin",
    //       *     operationId="updateHargaBarang",
    //       *     @OA\RequestBody(
    //       *         @OA\JsonContent(
    //       *               type="object",
    //       *               @OA\Property(property="barang_id"),
    //       *               @OA\Property(property="harga_barang"),
    //       *               @OA\Property(property="satuan"),
    //       *
    //       *     ),
    //       *         @OA\Schema(
    //       *               type="object",
    //       *               @OA\Property(property="barang_id", type="text"),
    //       *               @OA\Property(property="harga_barang", type="text"),
    //       *               @OA\Property(property="satuan", type="text"),
    //       *
    //       *
    //       *         ),
    //       *     ),
    //       *     @OA\Response(
    //       *         response="default",
    //       *         description="successful operation"
    //       *     )
    //       *)
    //       *
    //         */
    //     public function updateHarga(Request $request){
    //         DB::beginTransaction();
    //         try{
    //             $validator = Validator::make($request->all(),[
    //                 'barang_id'=>'required',
    //                 'harga_barang'=>'required',
    //                 'satuan'=>'required',
    //             ]);
    //             if($validator->fails()){
    //                 DB::commit();
    //                 return response()->json([
    //                     'status'=>false,
    //                     'message'=>$validator->errors()->first()
    //                 ],400);
    //             }
    //             $barang = BarangHistory::where('barang_id',$request->barang_id)->orderBy('created_at','DESC')->first();
    //             $before = $barang->harga_barang;
    //             BarangHistory::create([
    //                 'barang_id'=>$request->barang_id,
    //                 'harga_barang'=>$request->harga_barang,
    //                 'harga_sebelumnya'=>$before,
    //                 'satuan'=>$request->satuan,
    //                 'operation'=>'UPDATE',
    //                 'user_id'=>$request->user()->id,
    //             ]);
    //             DB::commit();
    //             return response()->json([
    //                 'status'=>true,
    //                 'message'=>'Update Harga Berhasil'
    //             ]);
    //         }catch (Throwable $e) {
    //             return response()->json([
    //                 'message' => $e->getMessage(),
    //                 'serve' => [],
    //             ], 500);
    //         }
    //     }

    /**
          * @OA\Post(
          *      security={{
          *        "bearerAuth":{}
          *      }},
          *     path="/barang/updateharga",
          *     tags={"Projects UMKM"},
          *     summary="Update Harga from Log By Barang and Toko",
          *     description="Update Harga from Log By Barang and Toko",
          *     operationId="updatehargaLog",
          *     @OA\RequestBody(
          *         @OA\JsonContent(
          *               type="object",
          *               @OA\Property(property="barang_id"),
          *               @OA\Property(property="toko_id"),
          *               @OA\Property(property="harga_barang"),
          *               @OA\Property(property="satuan"),
          *
          *     ),
          *         @OA\Schema(
          *               type="object",
          *               @OA\Property(property="barang_id", type="text"),
          *               @OA\Property(property="toko_id", type="text"),
          *               @OA\Property(property="harga_barang", type="text"),
          *               @OA\Property(property="satuan", type="text"),
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

            public function updateharga(Request $request){
                DB::beginTransaction();
                try{
                    $validator = Validator::make($request->all(),[
                        'barang_id'=>'required',
                        'toko_id'=>'required',
                        'harga_barang'=>'required',
                        'satuan'=>'required',
                    ]);
                    if($validator->fails()){
                        DB::commit();
                        return response()->json([
                            'status'=>false,
                            'message'=>$validator->errors()->first()
                        ],400);
                    }
                    $barang = BarangHistory::where('barang_id',$request->barang_id)->where('toko_id',$request->toko_id)->orderBy('created_at','DESC')->first();
                    $before = $barang->harga_barang;
                    BarangHistory::create([
                        'barang_id'=>$request->barang_id,
                        'toko_id'=>$request->toko_id,
                        'harga_barang'=>$request->harga_barang,
                        'harga_sebelumnya'=>$before,
                        'satuan'=>$request->satuan,
                        'operation'=>'UPDATE',
                        'user_id'=>$request->user()->id,
                    ]);
                    DB::commit();
                    return response()->json([
                        'status'=>true,
                        'message'=>'Update Harga Berhasil'
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
          *     path="/filter/barang",
          *     tags={"Projects UMKM"},
          *     summary="Filter Data Barang By Toko",
          *     description="Filter Data Barang By Toko",
          *     operationId="filterBarang",
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
          *     )
          *)
          *
            */

        public function filtertoko(Request $request){
            try{
                $name = $request->query('name');
                $size = $request->pageSize ? $request->pageSize:10;
                $barang = BarangToko::when($name,function ($query) use ($name){
                    return $query->where('nama_barang','like','%'.$name.'%')->join('barang','barang_toko.barang_id','=','barang.id');
                })->where('toko_id',$request->toko)->orderBy('barang_toko.created_at','desc')->select('barang_toko.*')->paginate($size);
                // $barang = BarangToko::where('toko_id',$request->toko)->get();
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



                // public function gewt(Request $request){
        //     try{
        //         $name = $request->query('name');
        //         $limit = $request->query('limit');
        //         $barang = Barang::when($name,function ($query) use ($name){
        //             return $query->where('nama_barang','like','%'.$name.'%');
        //         })->orderBy('created_at','desc')->paginate($limit);
        //         $tempArr = [];
        //         foreach($barang as $key => $value){
        //             $detail = BarangHistory::where('barang_id',$value->id)->orderBy('created_at','desc')->orderBy('updated_at','desc')->first();
        //             $satuan = Satuan::where('id',$detail->satuan)->first();
        //             $tempArr[$key]['id'] = $value->id;
        //             $tempArr[$key]['nama_barang'] = $value->nama_barang;
        //             $tempArr[$key]['harga_barang'] = $detail->harga_barang;
        //             $tempArr[$key]['satuan'] = $satuan->satuan;
        //             $tempArr[$key]['toko'] = $value->Toko->nama_toko;
        //             $tempArr[$key]['created_at'] = $value->created_at;
        //             $tempArr[$key]['updated_at'] = $value->updated_at;
        //         }
        //         return response()->json([
        //             'status'=>true,
        //             'data' => $tempArr,
        //         ],200);
        //     }catch (Throwable $e) {
        //         return response()->json([
        //             'message' => $e->getMessage(),
        //             'serve' => [],
        //         ], 500);
        //     }
        // }
}
