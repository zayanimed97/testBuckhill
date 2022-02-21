<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"Products"},
     *     path="/api/v1/product/create",
     *     summary="Create Product",
     *     security={ {"bearer": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"title","category_uuid","price","description"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="title",
     *                     description="Product title",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="category_uuid",
     *                     description="Category UUID",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="price",
     *                     description="Product price",
     *                     type="number"
     *                  ),
     *                  @OA\Property(
     *                     property="description",
     *                     description="Product description",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="metadata",
     *                     description="Product metadata",
     *                     type="object",
     *                     default={}
     *                  ),
     *             )
     *         )
     *     ), 
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     ),
     * )
     */
    public function create(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'title' => 'required',
            'category_uuid' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'metadata' => 'required|JSON',
        ]);

        if (!$validated->fails()) {
            $metadata = json_decode($request->metadata, true);
            $product = new Product();
            $product->uuid = (string) Str::uuid();
            $product->title = $request->title;
            $product->category_uuid = $request->category_uuid;
            $product->price = $request->price;
            $product->description = $request->description;
            $product->metadata = $metadata;
            $product->save();
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Products"},
     *     path="/api/v1/product/{uuid}",
     *     summary="Update Product",
     *     security={ {"bearer": {}} },
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         ),
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"title","category_uuid","price","description"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="title",
     *                     description="Product title",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="category_uuid",
     *                     description="Category UUID",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="price",
     *                     description="Product price",
     *                     type="number"
     *                  ),
     *                  @OA\Property(
     *                     property="description",
     *                     description="Product description",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="metadata",
     *                     description="Product metadata",
     *                     type="object",
     *                     default={}
     *                  ),
     *             )
     *         )
     *     ), 
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     ),
     * )
     */
    public function update($uuid, Request $request)
    {
        $validated = Validator::make($request->all(), [
            'title' => 'required',
            'category_uuid' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'metadata' => 'required|JSON',
        ]);

        if (!$validated->fails()) {
            $product = Product::where('uuid', $uuid)->first();
            if ($product) {
                $metadata = json_decode($request->metadata, true);
                $product->title = $request->title;
                $product->category_uuid = $request->category_uuid;
                $product->price = $request->price;
                $product->description = $request->description;
                $product->metadata = $metadata;
                $product->update();
            } else {
                return response()->json('Inexisting product', 404);
            }
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Products"},
     *     path="/api/v1/product/{uuid}",
     *     summary="Delete Product",
     *     security={ {"bearer": {}} },
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     ),
     * )
     */
    public function delete($uuid)
    {
        $product = Product::where('uuid', $uuid)->first();
        if ($product) {
            $product->delete();
        } else {
            return response()->json("inexisting product", 404);
        }

        return response()->json([]);
    }

    /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/api/v1/product/{uuid}",
     *     summary="Fetch a product",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     ),
     * )
     */
    public function getProduct($uuid)
    {
        $product = Product::with('brand', 'category')->where('uuid', $uuid)->first();
        if (!$product) {
            return response()->json("Inexisting product", 404);
        }
        return response()->json($product);
    }

    /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/api/v1/products",
     *     summary="List all products",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         @OA\Schema(
     *             type="boolean",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         @OA\Schema(
     *             type="number",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="brand",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     ),
     * )
     */
    public function products(Request $request)
    {
        DB::enableQueryLog();
        $products = Product::with('brand', 'category');

        if ($request->filled('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $products = $products->orderBy($request->sortBy, 'desc');
            } else {
                $products = $products->orderBy($request->sortBy);
            }
        }

        if ($request->filled('category')) {
            $products = $products->whereHas('category', function ($query) {
                $query->where('title', request('category'));
            });
        }

        if ($request->filled('price')) {
            $products = $products->where('price', $request->price);
        }

        if ($request->filled('brand')) {
            $products = $products->leftJoin('brands', DB::raw('brands.uuid'), 'metadata->brand')->where('brands.title', request('brand'))->select('products.*');
        }

        if ($request->filled('title')) {
            $products = $products->where('products.title', $request->title);
        }

        $products = $products->paginate($request->limit ?? 10);



        return response()->json($products);
    }
}
