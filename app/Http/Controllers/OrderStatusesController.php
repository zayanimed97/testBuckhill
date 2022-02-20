<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderStatusesController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"Order Statuses"},
     *     path="/api/v1/order-status/create",
     *     summary="Create Order Status",
     *     security={ {"bearer": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"title"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="title",
     *                     description="Order Status title",
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
            $orderStatus = new OrderStatus();
            $orderStatus->uuid = (string) Str::uuid();
            $orderStatus->title = $request->title;
            $orderStatus->save();
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Order Statuses"},
     *     path="/api/v1/order-status/{uuid}",
     *     summary="Update Order Status",
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
     *                     description="Order Status title",
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
            $OrderStatus = OrderStatus::where('uuid', $uuid)->first();
            if ($OrderStatus) {
                $OrderStatus->title = $request->title;
                $OrderStatus->update();
            } else {
                return response()->json('Inexisting Order Status', 404);
            }
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Order Statuses"},
     *     path="/api/v1/order-status/{uuid}",
     *     summary="Delete Order Status",
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
        $OrderStatus = OrderStatus::where('uuid', $uuid)->first();
        if ($OrderStatus) {
            $OrderStatus->delete();
        } else {
            return response()->json("inexisting Order Status", 404);
        }

        return response()->json([]);
    }

    /**
     * @OA\Get(
     *     tags={"Order Statuses"},
     *     path="/api/v1/order-status/{uuid}",
     *     summary="Fetch an Order Status",
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
    public function getOrderStatus($uuid)
    {
        $OrderStatus = OrderStatus::where('uuid', $uuid)->first();
        if (!$OrderStatus) {
            return response()->json("Inexisting Order Status", 404);
        }
        return response()->json($OrderStatus);
    }

                /**
     * @OA\Get(
     *     tags={"Order Statuses"},
     *     path="/api/v1/order-statuses",
     *     summary="List all Order Statuses",
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
    public function OrderStatuses(Request $request)
    {
        $orderStatuses = new OrderStatus();

        if ($request->has('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $orderStatuses = $orderStatuses->orderBy($request->sortBy, 'desc');
            } else {
                $orderStatuses = $orderStatuses->orderBy($request->sortBy);
            }
        }


        $orderStatuses = $orderStatuses->paginate($request->limit ?? 10);

        

        return response()->json($orderStatuses);
    }
}
