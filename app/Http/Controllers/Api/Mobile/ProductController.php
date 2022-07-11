<?php
namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Barang;
use App\Models\BarangHistory;
use App\Models\BarangToko;
use App\Models\Category;
use App\Models\Satuan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required',
        ]);
    }

    protected function validatorHistory(array $data)
    {
        return Validator::make($data, [
            'product_id' => 'required',
            'unit_id' => 'required',
        ]);
    }

    public function get(Request $request)
    {
        try {
            $name = $request->query('name');
            $sortBy = $request->query('sort_by') ? $request->query('sort_by') : "created_at";
            $sortByValue = $request->query('sort_by_value') ? $request->query('sort_by_value') : "desc";

            $dataProduct = Barang::when($name, function ($query) use ($name) {
                                        return $query->where('barang.nama_barang', 'like', '%' . $name . '%');
                                    })->orderBy("barang.".$sortBy, $sortByValue)
                                    ->paginate($request->pageSize);

            return response()->json([
                'message' => '',
                'serve' => $dataProduct,
            ], 200);
        } catch (\Throwable $e) {
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

            $dataProduct = new Barang();
            $dataProduct->nama_barang = $request->name;
            $dataProduct->merk = $request->merk;
            $dataProduct->category = $request->category;
            $dataProduct->save();

            DB::commit();
            return response()->json([
                'message' => 'Data baru berhasil ditambahkan.',
                'serve' => [],
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function retrieve(Request $request)
    {
        try {
            $dataProduct = BarangToko::where('id', $request->id)->first();
            if (!$dataProduct) {
                return response()->json([
                    'message' => "Gagal mendapatkan data.",
                    'serve' => []
                ], 400);
            }

            return response()->json([
                'message' => '',
                'serve' => $dataProduct,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }


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

            $dataProduct = Barang::where('id', $request->id)->first();
            if (!$dataProduct) {
                DB::commit();
                return response()->json([
                    'message' => "Gagal mendapatkan data.",
                    'serve' => []
                ], 400);
            }

            $dataProduct->nama_barang = $request->name;
            $dataProduct->save();
            DB::commit();
            return response()->json([
                'message' => 'Data berhasil diubah.',
                'serve' => $dataProduct,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }
    
    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataProduct = Barang::where('id', $request->id)->first();
            if (!$dataProduct) {
                DB::commit();
                return response()->json([
                    'message' => "Gagal mendapatkan data.",
                    'serve' => []
                ], 400);
            }

            $dataProduct->delete();
            DB::commit();
            return response()->json([
                'message' => 'Data berhasil dihapus.',
                'serve' => [],
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function history(Request $request)
    {
        try {
            $dataProduct = BarangHistory::select("barang.nama_barang", "barang_history.*", "barang_history.created_at as release")
                                        ->join("barang", "barang.id", "=", "barang_history.barang_id")
                                        ->where('barang_id', $request->query('id'))
                                        ->where('toko_id', Auth::user()->store->id)
                                        ->orderBy("barang_history.created_at","desc")
                                        ->paginate(10);
            if (!$dataProduct) {
                return response()->json([
                    'message' => "Gagal mendapatkan data.",
                    'serve' => []
                ], 400);
            }

            return response()->json([
                'message' => '',
                'serve' => $dataProduct,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function createHistory(Request $request)
    {
        DB::beginTransaction();
        try {
            $validate = $this->validatorHistory($request->all());
            if ($validate->fails()) {
                DB::commit();
                return response()->json([
                    'message' => $validate->errors()->first(),
                    'serve' => []
                ], 400);
            }

            $dataProduct = new BarangHistory;
            $dataProduct->barang_id = $request->product_id;
            $dataProduct->harga_barang = $request->price_after;
            $dataProduct->harga_sebelumnya = $request->price_before;
            $dataProduct->satuan = $request->unit_id;
            $dataProduct->operation = "CREATE";
            $dataProduct->created_at = $request->periode;
            $dataProduct->user_id = Auth::user()->id;
            $dataProduct->toko_id = Auth::user()->store->id;
            $dataProduct->save();

            $dataProductToko = BarangToko::where("barang_id", $request->product_id)
                                    ->where("toko_id", Auth::user()->store->id)
                                    ->first();
            if (!$dataProductToko) {
                $dataProductToko = new BarangToko();
                $dataProductToko->barang_id = $request->product_id;
                $dataProductToko->toko_id = Auth::user()->store->id;
                $dataProductToko->save();
            }
            DB::commit();
            return response()->json([
                'message' => 'Data baru berhasil ditambahkan.',
                'serve' => [],
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function deleteHistory(Request $request)
    {
        DB::beginTransaction();
        try {
            $dataProduct = BarangHistory::where('id', $request->id)->first();
            if (!$dataProduct) {
                DB::commit();
                return response()->json([
                    'message' => "Gagal mendapatkan data.",
                    'serve' => []
                ], 400);
            }

            $dataProduct->delete();
            DB::commit();
            return response()->json([
                'message' => 'Data berhasil dihapus.',
                'serve' => [],
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function getUnit(Request $request)
    {
        try {
            $dataStoreType = Satuan::orderBy("satuan", "asc")->get();
            return response()->json([
                'message' => '',
                'serve' => $dataStoreType,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function getCategory(Request $request)
    {
        try {
            $cat = Category::orderBy("category", "asc")->get();
            return response()->json([
                'message' => '',
                'serve' => $cat,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function getReport(Request $request) {
        try {
            $dataHistory = [];
            if ($request->query('type') == "1D") {
                $dataHistory = DB::table('barang_history')->select(DB::raw("ROUND(avg(harga_barang),0) as value"), DB::raw("CAST(((min(created_at) div 500)*500 + 230) AS DATETIME) as date"))
                                            ->whereDate("created_at", Carbon::today())
                                            ->where("barang_id", $request->query('barang_id'))
                                            ->groupByRaw("created_at div 500")
                                            ->get();
            }

            if ($request->query('type') == "7D") {
                $dataHistory = DB::table('barang_history')->select(DB::raw("ROUND(avg(harga_barang),0) as value"), DB::raw("CAST(created_at AS DATE) as date"))
                                            ->whereBetween("created_at", [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                                            ->where("barang_id", $request->query('barang_id'))
                                            ->groupBy("date")
                                            ->get();
            }

            if ($request->query('type') == "1M") {
                $dataHistory = DB::table('barang_history')->select(DB::raw("ROUND(avg(harga_barang),0) as value"), DB::raw("CAST(created_at AS DATE) as date"))
                                            ->whereMonth('created_at', date('m'))
                                            ->whereYear('created_at', date('Y'))
                                            ->where("barang_id", $request->query('barang_id'))
                                            ->groupBy("date")
                                            ->get();
            }

            if ($request->query('type') == "1Y") {
                $dataHistory = DB::table('barang_history')->select(DB::raw("ROUND(avg(harga_barang),0) as value"), DB::raw("MONTH(created_at) as date"))
                                            ->whereYear('created_at', date('Y'))
                                            ->where("barang_id", $request->query('barang_id'))
                                            ->groupBy("date")
                                            ->get();
            }

            if ($request->query('type') == "ALL") {
                $dataHistory = DB::table('barang_history')->select(DB::raw("ROUND(avg(harga_barang),0) as value"), DB::raw("YEAR(created_at) as date"))
                                            ->where("barang_id", $request->query('barang_id'))
                                            ->groupBy("date")
                                            ->get();
            }

            return response()->json([
                'message' => '',
                'serve' => $dataHistory,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }

    public function getExplore(Request $request) {
        try {
            $dataHistory = BarangHistory::with("toko")->with("barang")->orderBy("created_at", "desc")->paginate(5);
            return response()->json([
                'message' => '',
                'serve' => $dataHistory,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'serve' => [],
            ], 500);
        }
    }
}
