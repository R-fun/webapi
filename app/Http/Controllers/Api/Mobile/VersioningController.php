<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use NlpTools\Similarity\CosineSimilarity;
use NlpTools\Tokenizers\WhitespaceTokenizer; 

class VersioningController extends Controller
{
    public function get(Request $request)
    {
        try {
            $dataVersion = [
                "version" => "1.0.2",
                "link"    => "https://google.com"
            ];
            
            return response()->json([
                'message' => '',
                'serve' => $dataVersion
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