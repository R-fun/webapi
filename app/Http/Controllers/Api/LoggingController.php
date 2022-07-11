<?php

namespace App\Http\Controllers\Api;

use App\Exports\BarangHistoryExport;
use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangHistory;
use App\Models\BarangToko;
use App\Models\Toko;
use App\Models\Satuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Excel;
use Throwable;

class LoggingController extends Controller
{
    /**
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/log",
      *     tags={"Projects UMKM"},
      *     summary="Looking Data Log",
      *     description="Looking Data Log",
      *     operationId="getLog",
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
                $dataSatuan = BarangHistory::with('Barang','Toko')->when($name,function ($query) use ($name) {
                    return $query->where('operation', 'like', '%' . $name . '%');
                })->orderBy("created_at", "desc")->paginate($size);
                return response()->json([
                    'message' => '',
                    'log' => $dataSatuan,
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
      *     path="/log/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Get By Id Data Logging",
      *     description="Get By Id Data Logging",
      *     operationId="getByIdLogging",
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

        public function getbyid($id){
            try{
                if(!BarangHistory::where('barang_id',$id)->get()){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Data Tidak Ditemukan'
                    ],404);
                }
                $log = BarangHistory::with('Barang')->where('barang_id',$id)->orderBy('created_at','DESC')->first();
                return response()->json([
                    'status' => true,
                    'log'=>$log
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
          *     path="/getdata/log",
          *     tags={"Projects UMKM"},
          *     summary="get Data Log By Barang and Toko",
          *     description="get Data Log By Barang and Toko",
          *     operationId="getDataLog",
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

        public function getData(Request $request){
            try{
                if(!BarangHistory::where('barang_id',$request->barang_id)->where('toko_id',$request->toko_id)->first()){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Data Tidak Ditemukan'
                    ],404);
                }
                $log = BarangHistory::with(['Barang','Toko'])->where('barang_id',$request->barang_id)->where('toko_id',$request->toko_id)->orderBy('created_at','DESC')->first();
                return response()->json([
                    'status' => true,
                    'log'=>$log
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
          *     path="/filter/log",
          *     tags={"Projects UMKM"},
          *     summary="Filter Data Log By Barang",
          *     description="Filter Data Log By Barang",
          *     operationId="filterLog",
          *     @OA\RequestBody(
          *         @OA\JsonContent(
          *               type="object",
          *               @OA\Property(property="tanggalmulai"),
          *               @OA\Property(property="tanggalakhir"),
          *               @OA\Property(property="toko"),
          *               @OA\Property(property="barang_id"),
          *
          *     ),
          *         @OA\Schema(
          *               type="object",
          *               @OA\Property(property="tanggalmulai", type="text"),
          *               @OA\Property(property="tanggalakhir", type="text"),
          *               @OA\Property(property="toko", type="text"),
          *               @OA\Property(property="barang_id", type="text"),
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

            public function filterlog(Request $request){
                try{
                    $size = $request->pageSize ? $request->pageSize:10;
                    $log = BarangHistory::with('Barang')
                    ->whereBetween('created_at',[$request->tanggalmulai,$request->tanggalakhir])->where('barang_id',$request->barang_id)->where('toko_id',$request->toko)->paginate($size);
                    return response()->json([
                        'status'=>true,
                        'data'=>$log
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
      *     path="/order/log",
      *     tags={"Projects UMKM"},
      *     summary="Looking Data Log order",
      *     description="Looking Data Log order",
      *     operationId="getorderLog",
      *
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     )
      *)
      *
    */

    public function orderlog(){
        try {
            $log = BarangHistory::with('Barang')->whereDate('created_at',Carbon::today())->orderBy('created_at','DESC')->offset(0)->limit(5)->get();
            return response()->json([
                'message' => '',
                'log' => $log,
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
      *     path="/export/log/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Export Data Logging",
      *     description="Export Data Logging",
      *     operationId="exportLogging",
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

    public function exportlog($id){
        try{
            $tempArr = [];
            $barang = BarangToko::where('toko_id',$id)->orderBy('created_at','desc')->get();
            $toko = Toko::where('id',$id)->first();
            foreach($barang as $br){
                $detail = BarangHistory::where('barang_id',$br->barang_id)->where('toko_id',$br->toko_id)->orderBy('created_at','desc')->first();
                $tempArr[]=[
                    $detail->created_at,
                    $detail->Barang->nama_barang,
                    $detail->harga_barang
                ];
            }
            $export = new BarangHistoryExport([
                ['Nama Toko: ',$toko->nama_toko],
                ['Tanggal','Nama Produk','Harga'],
                $tempArr
             ]);
            // $export = new BarangHistoryExport($log);
            return Excel::download($export,'baranghistory.xlsx');
        }catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }
}
