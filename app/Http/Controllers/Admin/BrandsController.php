<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BrandsController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"Brands"},
     *     path="/api/v1/brand/create",
     *     summary="Create brand",
     *     security={ {"bearer": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"title"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="title",
     *                     description="Brand title",
     *                     type="string"
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
        ]);

        if (!$validated->fails()) {
            $brand = new Brand();
            $brand->uuid = (string) Str::uuid();
            $brand->title = $request->title;
            $brand->slug = Str::slug($request->title);
            $brand->save();
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Brands"},
     *     path="/api/v1/brand/{uuid}",
     *     summary="Update a brand",
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
     *                  required={"title"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="title",
     *                     description="Brand title",
     *                     type="string"
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
        ]);

        if (!$validated->fails()) {
            $brand = Brand::where('uuid', $uuid)->first();
            if ($brand) {
                $brand->title = $request->title;
                $brand->slug = Str::slug($request->title);
                $brand->update();
            } else {
                return response()->json('Inexisting brand', 404);
            }
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Brands"},
     *     path="/api/v1/brand/{uuid}",
     *     summary="Delete Brand",
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
        $brand = Brand::where('uuid', $uuid)->first();
        if ($brand) {
            $brand->delete();
        } else {
            return response()->json("inexisting brand", 404);
        }

        return response()->json([]);
    }

    /**
     * @OA\Get(
     *     tags={"Brands"},
     *     path="/api/v1/brand/{uuid}",
     *     summary="Fetch a brand",
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
    public function getBrand($uuid)
    {
        $brand = Brand::where('uuid', $uuid)->first();
        if (!$brand) {
            return response()->json("Inexisting brand", 404);
        }
        return response()->json($brand);
    }

                /**
     * @OA\Get(
     *     tags={"Brands"},
     *     path="/api/v1/brands",
     *     summary="List all brands",
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
    public function brands(Request $request)
    {
        $brands = new Brand();

        if ($request->has('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $brands = $brands->orderBy($request->sortBy, 'desc');
            } else {
                $brands = $brands->orderBy($request->sortBy);
            }
        }


        $brands = $brands->paginate($request->limit ?? 10);

        

        return response()->json($brands);
    }
}
