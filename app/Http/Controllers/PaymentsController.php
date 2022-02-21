<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentsController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"Payments"},
     *     path="/api/v1/payment/create",
     *     summary="Create Payment",
     *     security={ {"bearer": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"type", "details"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="type",
     *                     description="Payment type",
     *                     type="string",
     *                     enum={"credit_card", "cash_on_delivery", "bank_transfer"}
     *                  ),
     *                  @OA\Property(
     *                     property="details",
     *                     description="Review documentation for the payment type JSON format",
     *                     type="object",
     *                     default="{}"
     *                  ),
     *                  @OA\Property(
     *                     property="title",
     *                     description="Payment Title",
     *                     type="string",
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
            'type' => Rule::in(['credit_card','cash_on_delivery', 'bank_transfer']),
            'details' => 'required|JSON',
        ]);


        if (!$validated->fails()) {
            $details = json_decode($request->details, true);
            $payment = new Payment();
                $payment->uuid = (string) Str::uuid();
                $payment->type = $request->type;
                $payment->title = $request->title;
                $payment->details = $details;
            $payment->save();
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Payments"},
     *     path="/api/v1/payment/{uuid}",
     *     summary="Update Payment",
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
     *                     description="Payment title",
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
            $Payment = Payment::where('uuid', $uuid)->first();
            if ($Payment) {
                $Payment->title = $request->title;
                $Payment->update();
            } else {
                return response()->json('Inexisting Payment', 404);
            }
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Payments"},
     *     path="/api/v1/payment/{uuid}",
     *     summary="Delete Payment",
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
        $Payment = Payment::where('uuid', $uuid)->first();
        if ($Payment) {
            $Payment->delete();
        } else {
            return response()->json("inexisting Payment", 404);
        }

        return response()->json([]);
    }

    /**
     * @OA\Get(
     *     tags={"Payments"},
     *     path="/api/v1/payment/{uuid}",
     *     summary="Fetch a Payment",
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
    public function getPayment($uuid)
    {
        $Payment = Payment::where('uuid', $uuid)->first();
        if (!$Payment) {
            return response()->json("Inexisting Payment", 404);
        }
        return response()->json($Payment);
    }

                /**
     * @OA\Get(
     *     tags={"Payments"},
     *     path="/api/v1/payments",
     *     summary="List all Payments",
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
    public function payments(Request $request)
    {
        $payment = new Payment();

        if ($request->filled('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $payment = $payment->orderBy($request->sortBy, 'desc');
            } else {
                $payment = $payment->orderBy($request->sortBy);
            }
        }


        $payment = $payment->paginate($request->limit ?? 10);

        

        return response()->json($payment);
    }
}
