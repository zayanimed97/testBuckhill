<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Promotion;
use Illuminate\Http\Request;

class MainPageController extends Controller
{
            /**
     * @OA\Get(
     *     tags={"MainPage"},
     *     path="/api/v1/main/promotions",
     *     summary="List all promotions",
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
     *         name="valid",
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
    public function promotions(Request $request)
    {
        $promotions = new Promotion;

        if ($request->filled('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $promotions = $promotions->orderBy($request->sortBy, 'desc');
            } else {
                $promotions = $promotions->orderBy($request->sortBy);
            }
        }

        if ($request->filled('valid') && $request->valid == 'true') {
            $promotions = $promotions->whereRaw("'".date('Y-m-d').'"' ." between IF(JSON_VALID(metadata), JSON_UNQUOTE( JSON_EXTRACT(metadata, '$.valid_from')),'') and IF(JSON_VALID(metadata), JSON_UNQUOTE( JSON_EXTRACT(metadata, '$.valid_to')),'')");
        }

        $promotions = $promotions->paginate($request->limit ?? 10);

        

        return response()->json($promotions);
    }

            /**
     * @OA\Get(
     *     tags={"MainPage"},
     *     path="/api/v1/main/blog",
     *     summary="List all posts",
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
    public function blog(Request $request)
    {
        $posts = new Post();

        if ($request->filled('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $posts = $posts->orderBy($request->sortBy, 'desc');
            } else {
                $posts = $posts->orderBy($request->sortBy);
            }
        }


        $posts = $posts->paginate($request->limit ?? 10);

        

        return response()->json($posts);
    }

                /**
     * @OA\Get(
     *     tags={"MainPage"},
     *     path="/api/v1/main/blog/{uuid}",
     *     summary="List all posts",
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
    public function blogPost($uuid)
    {
        $post = Post::where('uuid', $uuid)->first();

        return response()->json($post);
    }
}
