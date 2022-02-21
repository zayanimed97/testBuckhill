<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"Files"},
     *     path="/api/v1/file/upload",
     *     summary="Upload a file",
     *     security={ {"bearer": {}} },
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"file"},
     *                  type="object",
     *                  @OA\Property(
     *                      description="file to upload",
     *                      property="file",
     *                      type="string",
     *                      format="binary",
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

    public function upload(request $request)
    {
        $validated = Validator::make($request->all(), [
            'file' => 'required|file',
        ]);

        if (!$validated->fails()) {
            $name = (string) Str::uuid();
            $path = Storage::putFileAs(
                'uploads/pet-shop/',
                $request->file('file'),
                $name . '.' . $request->file('file')->getClientOriginalExtension()
            );
            $file = new File();
            $file->uuid = $name;
            $file->name = $request->file('file')->getClientOriginalName();
            $file->path = $path;
            $file->size = $request->file('file')->getSize();
            $file->type = $request->file('file')->getClientOriginalExtension();
            $file->save();
            return response()->json($name);
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Get(
     *     tags={"Files"},
     *     path="/api/v1/file/{uuid}",
     *     summary="Read a file",
     *     @OA\Parameter(
     *         name="uuid",
     *         required=true,
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

    public function download($uuid)
    {
        $file = File::where('uuid', $uuid)->first();

        if ($file) {
            return Storage::download($file->path);
        } else {
            return response()->json("file doesn't exist", 404);
        }
    }
}
