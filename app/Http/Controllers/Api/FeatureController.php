<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangToko;
use App\Models\BarangHistory;
use App\Models\Toko;
use Illuminate\Http\Request;
use Throwable;

class FeatureController extends Controller
{

    // /**
    //       * @OA\Post(
    //       *      security={{
    //       *        "bearerAuth":{}
    //       *      }},
    //       *     path="/compare/barang",
    //       *     tags={"Projects UMKM"},
    //       *     summary="Get Data For Compare Feature",
    //       *     description="Get Data For Compare Feature",
    //       *     operationId="getCompareBarang",
    //       *     @OA\RequestBody(
    //       *         @OA\JsonContent(
    //       *               type="object",
    //       *               @OA\Property(property="barang_id"),
    //       *
    //       *     ),
    //       *         @OA\Schema(
    //       *               type="object",
    //       *               @OA\Property(property="barang_id", type="text"),
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

    //     public function getCompareData(Request $request){
    //         try{
    //             $toko = Barang::with('Toko')->where('id',$request->barang_id)->get();
    //             return response()->json([
    //                 'message' => '',
    //                 'serve' => $toko,
    //             ], 200);
    //         }catch (Throwable $e) {
    //             return response()->json([
    //                 'message' => $e->getMessage(),
    //                 'serve' => [],
    //             ], 500);
    //         }
    //     }


    /**
          * @OA\Get(
          *      security={{
          *        "bearerAuth":{}
          *      }},
          *     path="/compare/barang",
          *     tags={"Projects UMKM"},
          *     summary="Compare Feature Barang Admin",
          *     description="Compare Feature Barang Admin",
          *     operationId="compareFeature",
          *      @OA\Parameter(
          *         name="barang_id",
          *         in="query",
          *         description="Id Barang",
          *         required=true,
          *      ),
          *     @OA\Response(
          *         response="default",
          *         description="successful operation"
          *     )
          *)
          *
            */

        public function compareFeature(Request $request){
            try{
                $name = $request->query('name');
                $barang_id = $request->query('barang_id');
                $size = $request->pageSize ? $request->pageSize:10;
                $toko = BarangToko::when($name,function ($query) use ($name){
                    return $query->where('nama_toko','like','%'.$name.'%')->join('toko','barang_toko.toko_id','=','toko.id');
                })->where('barang_toko.barang_id',$barang_id)->orderBy('barang_toko.created_at','desc')->select('barang_toko.*')->paginate($size);
                // $toko = BarangToko::where('barang_id',$request->barang_id)->get();
                return response()->json([
                    'message' => '',
                    'serve' => $toko,
                ], 200);
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
          *     path="/compare",
          *     tags={"Projects UMKM"},
          *     summary="Compare Price Barang Admin",
          *     description="Compare Price Barang Admin",
          *     operationId="comparePrice",
          *     @OA\RequestBody(
          *         @OA\JsonContent(
          *               type="object",
          *               @OA\Property(property="barang_id"),
          *               @OA\Property(property="first_toko_id"),
          *               @OA\Property(property="second_toko_id"),
          *
          *     ),
          *         @OA\Schema(
          *               type="object",
          *               @OA\Property(property="barang_id", type="text"),
          *               @OA\Property(property="first_toko_id", type="text"),
          *               @OA\Property(property="second_toko_id", type="text"),
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

        public function comparePrice(Request $request){
            try{
                $barang = $request->barang_id;
                $first_toko = BarangToko::where('toko_id',$request->first_toko_id)->where('barang_id',$barang)->orderBy('created_at','desc')->first();
                $second_toko = BarangToko::where('toko_id',$request->second_toko_id)->where('barang_id',$barang)->orderBy('created_at','desc')->first();
                $first_log = BarangHistory::with('Barang')->where('toko_id',$request->first_toko_id)->where('barang_id',$barang)->orderBy('created_at','desc')->offset(0)->limit(10)->get();
                $second_log = BarangHistory::with('Barang')->where('toko_id',$request->second_toko_id)->where('barang_id',$barang)->orderBy('created_at','desc')->offset(0)->limit(10)->get();
                return response()->json([
                    'message' => '',
                    'serve_first' => $first_toko,
                    'serve_second' => $second_toko,
                    'first_log'=> $first_log,
                    'second_log'=> $second_log
                ], 200);
            }catch (Throwable $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'serve' => [],
                    'serve2' => [],
                ], 500);
            }
        }
}
