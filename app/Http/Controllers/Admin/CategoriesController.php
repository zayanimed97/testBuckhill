<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"Categories"},
     *     path="/api/v1/category/create",
     *     summary="Create category",
     *     security={ {"bearer": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"title"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="title",
     *                     description="Category title",
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
            $category = new Category();
            $category->uuid = (string) Str::uuid();
            $category->title = $request->title;
            $category->slug = Str::slug($request->title);
            $category->save();
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Categories"},
     *     path="/api/v1/category/{uuid}",
     *     summary="Login an admin account",
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
     *                     description="Category title",
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
            $category = Category::where('uuid', $uuid)->first();
            if ($category) {
                $category->title = $request->title;
                $category->slug = Str::slug($request->title);
                $category->update();
            } else {
                return response()->json('Inexisting category', 404);
            }
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Categories"},
     *     path="/api/v1/category/{uuid}",
     *     summary="Delete Category",
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
        $category = Category::where('uuid', $uuid)->first();
        if ($category) {
            $category->delete();
        } else {
            return response()->json("inexisting category", 404);
        }

        return response()->json([]);
    }

    /**
     * @OA\Get(
     *     tags={"Categories"},
     *     path="/api/v1/category/{uuid}",
     *     summary="Fetch a category",
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
    public function getCategory($uuid)
    {
        $category = Category::where('uuid', $uuid)->first();
        if (!$category) {
            return response()->json("Inexisting category", 404);
        }
        return response()->json($category);
    }

                /**
     * @OA\Get(
     *     tags={"Categories"},
     *     path="/api/v1/categories",
     *     summary="List all categories",
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
    public function categories(Request $request)
    {
        $categories = new Category();

        if ($request->filled('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $categories = $categories->orderBy($request->sortBy, 'desc');
            } else {
                $categories = $categories->orderBy($request->sortBy);
            }
        }


        $categories = $categories->paginate($request->limit ?? 10);

        

        return response()->json($categories);
    }
}
