<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Toko;
use App\Models\JenisToko;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class StoreController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required',
            'type' => 'required',
        ]);
    }

    public function getStoreType(Request $request)
    {
        try {
            $dataStoreType = JenisToko::orderBy("jenis_toko", "asc")->get();
            return response()->json([
                'message' => '',
                'serve' => $dataStoreType,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

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

            $dataStore = new Toko;
            $dataStore->nama_toko = $request->name;
            $dataStore->jenis_toko = $request->type;
            $dataStore->alamat = $request->address;
            $dataStore->user_id = Auth::user()->id;
            $dataStore->save();

            DB::commit();
            return response()->json([
                'message' => '',
                'serve' => $dataStore,
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
