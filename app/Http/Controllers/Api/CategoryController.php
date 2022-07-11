<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CategoryController extends Controller
{
    protected function validator(array $data){
        return Validator::make($data,[
            'category'=>'required',
        ]);
    }
    /**
      * @OA\Get(
      *      security={{
      *        "bearerAuth":{}
      *      }},
      *     path="/category",
      *     tags={"Projects UMKM"},
      *     summary="Looking Data Category",
      *     description="Looking Data Category",
      *     operationId="getCategory",
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
                $category = Category::when($name,function ($query) use ($name){
                    return $query->where('category','like','%'.$name.'%');
                })->orderBy('created_at','desc')->paginate($size);
                return response()->json([
                    'status'=>true,
                    'data' => $category,
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
      *     path="/category/{id}",
      *     tags={"Projects UMKM"},
      *     summary="Get By Id Data Category",
      *     description="Get By Id Data Category",
      *     operationId="getByIdCategory",
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
                if(!Category::find($id)){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Data Tidak Ditemukan'
                    ],404);
                }

                $category = Category::where('id',$id)->get();

                return response()->json([
                    'status'=>true,
                    'category'=> $category,
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
          *     path="/category",
          *     tags={"Projects UMKM"},
          *     summary="Insert Data Category Admin",
          *     description="Insert Data Category Admin",
          *     operationId="insertCategory",
          *     @OA\RequestBody(
          *         @OA\JsonContent(
          *               type="object",
          *               @OA\Property(property="category"),
          *
          *     ),
          *         @OA\Schema(
          *               type="object",
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

                Category::create([
                    'category'=>$request->category,
                ]);

                DB::commit();
                return response()->json([
                    'status'=>true,
                    'message'=>'Tambah Category Berhasil'
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
          *     path="/category/{id}",
          *     tags={"Projects UMKM"},
          *     summary="Update Data Category Admin",
          *     description="Update Data Category Admin",
          *     operationId="updateCategory",
          *     @OA\Parameter(
          *         name="id",
          *         in="path",
          *         description="Enter Id Data For Update",
          *         required=true,
          *     ),
          *     @OA\RequestBody(
          *         @OA\JsonContent(
          *               type="object",
          *               @OA\Property(property="category"),
          *
          *     ),
          *         @OA\Schema(
          *               type="object",
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

                if(!Category::find($id)){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Data Tidak Ditemukan'
                    ],404);
                }
                Category::where('id',$id)->update([
                    'category'=>$request->category,
                ]);

                DB::commit();
                return response()->json([
                    'status'=>true,
                    'message'=>'Edit Category Berhasil'
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
          *     path="/category/{id}",
          *     tags={"Projects UMKM"},
          *     summary="Delete Data Category Admin",
          *     description="Delete Data Category Admin",
          *     operationId="deleteCategory",
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
                if(!Category::find($id)){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Data Tidak Ditemukan'
                    ],404);
                }
                Category::where('id',$id)->delete();
                DB::commit();


                return response()->json([
                    'status'=>true,
                    'message'=>'Hapus Category Berhasil'
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
