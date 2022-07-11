<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'fullname' => 'required|string',
        ]);
    }

    public function changePassword(Request $request)
    {
        DB::beginTransaction();
        try {
            $validate = Validator::make($request->all(), [
                'old_password' => 'required',
                'password_confirmation' => 'required',
                'password' => 'required|confirmed',
            ]);
            if ($validate->fails()) {
                DB::commit();
                return response()->json([
                    'message' => $validate->errors()->first(),
                    'serve' => []
                ], 400);
            }

            $dataUser = User::where('id', $request->id)->first();
            if (!$dataUser) {
                DB::commit();
                return response()->json([
                    'message' => "Gagal mendapatkan data.",
                    'serve' => []
                ], 400);
            }
            if (!Hash::check($request->old_password, $dataUser->password)) {
                DB::commit();
                return response()->json([
                    'message' => "Password lama salah.",
                    'serve' => []
                ], 400);
            }

            $dataUser->password = bcrypt($request->password);
            $dataUser->save();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil Mengubah password.',
                'serve' => $dataUser,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function changeProfile(Request $request)
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

            $dataUser = User::where('id', $request->id)->first();
            if (!$dataUser) {
                DB::commit();
                return response()->json([
                    'message' => "Gagal mendapatkan data.",
                    'serve' => []
                ], 400);
            }

            $dataUser->name = $request->fullname;
            $dataUser->save();

            $dataToko = Toko::where("user_id", $request->id)->first();
            if (!$dataToko) {
                DB::commit();
                return response()->json([
                    'message' => "Gagal mendapatkan data.",
                    'serve' => []
                ], 400);
            }
            $dataToko->nama_toko = $request->name;
            $dataToko->jenis_toko = $request->type;
            $dataToko->alamat = $request->address;
            $dataToko->save();

            DB::commit();
            return response()->json([
                'message' => 'Data berhasil diubah.',
                'serve' => [
                    "user" => $dataUser,
                    "toko" => $dataToko,
                ]
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }
}
