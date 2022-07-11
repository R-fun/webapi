<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Satuan;
use Illuminate\Support\Facades\DB;
use Throwable;

class SatuanController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'satuan' => 'required',
        ]);
    }

    /**
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/satuan",
      *     tags={"Projects UMKM"},
      *     summary="Looking Data Satuan",
      *     description="Looking Data Satuan",
      *     operationId="getSatuan",
      *
      *     @OA\Response(
      *         response="default",
      *         description="successful operation"
      *     )
      *)
      *
    */

    public function get(Request $request)
    {
        try {
            $name = $request->query('name');
            $size = $request->pageSize ? $request->pageSize:10;
            $dataSatuan = Satuan::when($name, function ($query) use ($name) {
                return $query->where('satuan', 'like', '%' . $name . '%');
            })->orderBy("created_at", "desc")->paginate($size);
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
      * @OA\Post(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/satuan",
      *     tags={"Projects UMKM"},
      *     summary="Insert Data Satuan",
      *     description="Insert Data Satuan",
      *     operationId="insertSatuan",
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *               type="object",
      *               @OA\Property(property="satuan"),
      *
      *     ),
      *         @OA\Schema(
      *               type="object",
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

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validate = $this->validator($request->all());
            if ($validate->fails()) {
                DB::commit();
                return response()->json([
                    'message' => $validate->errors()->first(),
                    'serve' => []
                ], 400);
            }

            $dataSatuan = new Satuan;
            $dataSatuan->satuan = $request->satuan;
            $dataSatuan->save();

            DB::commit();
            return response()->json([
                'message' => 'Data baru berhasil ditambahkan.',
                'serve' => [],
            ], 200);
        } catch (Throwable $e) {
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
      *     path="/satuan/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Get By Id Data Satuan",
      *     description="Get By Id Data Satuan",
      *     operationId="getByIdSatuan",
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
    public function retrieve(Request $request)
    {
        try {
            $dataSatuan = Satuan::where("id", $request->id)->first();
            if (!$dataSatuan) {
                return response()->json([
                    'message' => 'Data tidak diketahui.',
                    'serve' => []
                ], 400);
            }

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
      * @OA\Put(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/satuan/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Update Data Satuan",
      *     description="Update Data Satuan",
      *     operationId="updateSatuan",
      *     @OA\Parameter(
      *         name="id",
      *         in="path",
      *         description="Enter Id Data For Update",
      *         required=true,
      *     ),
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *               type="object",
      *               @OA\Property(property="satuan"),
      *
      *     ),
      *         @OA\Schema(
      *               type="object",
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

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $validate = $this->validator($request->all());
            if ($validate->fails()) {
                DB::commit();
                return response()->json([
                    'message' => $validate->errors()->first(),
                    'serve' => []
                ], 400);
            }

            $dataSatuan = Satuan::where('id', $request->id)->first();
            if (!$dataSatuan) {
                DB::commit();
                return response()->json([
                    'message' => "Gagal mendapatkan data.",
                    'serve' => []
                ], 400);
            }

            $dataSatuan->satuan = $request->satuan;
            $dataSatuan->save();
            DB::commit();
            return response()->json([
                'message' => 'Data berhasil diubah.',
                'serve' => $dataSatuan,
            ], 200);
        } catch (Throwable $e) {
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
      *     path="/satuan/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Delete Data Satuan",
      *     description="Delete Data Satuan",
      *     operationId="deleteSatuan",
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

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataSatuan = Satuan::where('id', $request->id)->first();
            if (!$dataSatuan) {
                DB::commit();
                return response()->json([
                    'message' => "Gagal mendapatkan data.",
                    'serve' => []
                ], 400);
            }

            $dataSatuan->delete();
            DB::commit();
            return response()->json([
                'message' => 'Data berhasil dihapus.',
                'serve' => [],
            ], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }
}
