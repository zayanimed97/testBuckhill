<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;

class OrdersController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"Orders"},
     *     path="/api/v1/order/create",
     *     summary="Create order",
     *     security={ {"bearer": {}} },
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"order_status_uuid", "payment_uuid", "products", "address"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="order_status_uuid",
     *                     description="Order status UUID",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="payment_uuid",
     *                     description="Payment UUID",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="products",
     *                     description="Array of objects with product uuid and quantity",
     *                     type="array",
     *                      default="[]",
     *                     @OA\Items(
     *                          @OA\Property(
     *                               property="uuid",
     *                               type="string",
     *                               example="uuid"
     *                          ),
     *                          @OA\Property(
     *                               property="quantity",
     *                               type="int",
     *                               example=0
     *                          ),
     *                      )
     *                  ),
     *                  @OA\Property(
     *                     property="address",
     *                     description="Billing and Shipping address",
     *                     type="object",
     *                     default={"billing": "string","shipping": "string"}
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
            'order_status_uuid' => 'required',
            'payment_uuid' => 'required',
            'address' => 'required|json',
        ]);

        $products = json_decode($request->products) ?: json_decode('[' . $request->products . ']');

        // return response(json_encode($products));
        $product_ids = array_map(function ($el) {
            return $el->uuid ?? '';
        }, $products);
        $productPrices = Product::whereIn('uuid', $product_ids ?? [])->select('uuid', 'price')->get();

        $prices = [];
        foreach ($productPrices as $price) {
            $prod = array_filter($products, function ($el) use ($price) {
                return $el->uuid == $price->uuid;
            });
            array_push($prices, intval(reset($prod)->quantity) * floatval($price->price));
        }

        if (!$validated->fails()) {
            $order = new Order();
            $order->user_id = $request->session()->get('user')['id'];
            $order->uuid = (string) Str::uuid();
            $order->order_status_id = $request->order_status_uuid;
            $order->payment_id = $request->payment_uuid;
            $order->products = $products;
            $order->address = $request->address;
            $order->amount = array_sum($prices);
            $order->delivery_fee = array_sum($prices) > 500 ? 0 : 15;
            $order->save();
        } else {
            return response()->json($validated->errors(), 422);
        }
    }

    /**
     * @OA\Put(
     *     tags={"Orders"},
     *     path="/api/v1/order/{uuid}",
     *     summary="Update order",
     *     security={ {"bearer": {}} },
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         ),
     *      ),
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"order_status_uuid", "payment_uuid", "products", "address"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="order_status_uuid",
     *                     description="Order status UUID",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="payment_uuid",
     *                     description="Payment UUID",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="products",
     *                     description="Array of objects with product uuid and quantity",
     *                     type="array",
     *                      default="[]",
     *                     @OA\Items(
     *                          @OA\Property(
     *                               property="uuid",
     *                               type="string",
     *                               example="uuid"
     *                          ),
     *                          @OA\Property(
     *                               property="quantity",
     *                               type="int",
     *                               example=0
     *                          ),
     *                      )
     *                  ),
     *                  @OA\Property(
     *                     property="address",
     *                     description="Billing and Shipping address",
     *                     type="object",
     *                     default={"billing": "string","shipping": "string"}
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
            'order_status_uuid' => 'required',
            'payment_uuid' => 'required',
            'address' => 'required|json',
        ]);

        $products = gettype(json_decode($request->products)) == "array" ? json_decode($request->products) : json_decode('[' . $request->products . ']');
        $product_ids = array_map(function ($el) {
            return $el->uuid ?? '';
        }, $products);
        $productPrices = Product::whereIn('uuid', $product_ids ?? [])->select('uuid', 'price')->get();

        $prices = [];
        foreach ($productPrices as $price) {
            $prod = array_filter($products, function ($el) use ($price) {
                return $el->uuid == $price->uuid;
            });
            array_push($prices, intval(reset($prod)->quantity) * floatval($price->price));
        }

        if (!$validated->fails()) {
            $order = Order::where("uuid", $uuid)->first();
            if ($order) {
                $order->order_status_id = $request->order_status_uuid;
                $order->payment_id = $request->payment_uuid;
                $order->products = json_encode($request->products);
                $order->address = $request->address;
                $order->amount = array_sum($prices);
                $order->delivery_fee = array_sum($prices) > 500 ? 0 : 15;
                $order->update();
            } else {
                return response()->json('Inexisting Order', 404);
            }
        } else {
            return response()->json($validated->errors(), 422);
        }
    }
    /**
     * @OA\Get(
     *     tags={"Orders"},
     *     path="/api/v1/orders",
     *     summary="List all orders",
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
    public function orders(Request $request)
    {
        $orders = new Order();

        if ($request->has('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $orders = $orders->orderBy($request->sortBy, 'desc');
            } else {
                $orders = $orders->orderBy($request->sortBy);
            }
        }


        $orders = $orders->paginate($request->limit ?? 10);



        return response()->json($orders);
    }

    /**
     * @OA\Get(
     *     tags={"Orders"},
     *     path="/api/v1/order/{uuid}",
     *     summary="Fetch an order",
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
    public function getOrder($uuid)
    {
        $order = Order::where('uuid', $uuid)->first();
        if (!$order) {
            return response()->json("Inexisting order", 404);
        }
        return response()->json($order);
    }


    /**
     * @OA\Get(
     *     tags={"Orders"},
     *     path="/api/v1/orders/dashboard",
     *     summary="List all orders to populate the dashboard",
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
     *         name="dateRange",
     *         in="query",
     *         @OA\Schema(
     *             type="object",
     *             default={"from": "string", "to": "string"}
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="fixRange",
     *         in="query",
     *         schema={"type": "string", "enum": {"today", "monthly", "yearly"}}
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
    public function dashboard(Request $request)
    {
        //Orders List
        $orders = Order::with('status', 'user', 'products_relation');

        if ($request->has('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $orders = $orders->orderBy($request->sortBy, 'desc');
            } else {
                $orders = $orders->orderBy($request->sortBy);
            }
        }
        $orders = $orders->paginate($request->limit ?? 10);
        //end orders list

        //Orders Chart

        //default star end
        $start_date = Carbon::today()->subMonth();
        $end_date = Carbon::today()->addDay();
        $formatQuery = '%Y-%m-%d';
        $formatGroupBy = 'Y-m-d';
        $increment = "addDay";
        $formatResponse = 'm-d';

        //handle dateRange param
        if ($request->has('dateRange')) {
            $range = json_decode($request->dateRange);
            // dd($range);
            if ($range->from && $range->to) {
                $start_date = new Carbon($range->from);
                $end_date = new Carbon($range->to);
            }
        }

        //handle fixRange param
        if ($request->has('fixRange')) {
            if ($request->fixRange == 'monthly') {
                $start_date = Carbon::today()->subMonth();
                $end_date = Carbon::today()->addDay();
                $formatQuery = '%Y-%m-%d';
                $formatGroupBy = 'Y-m-d';
                $increment = "addDay";
                $formatResponse = 'm-d';
            } else if ($request->fixRange == 'today') {
                $start_date = Carbon::today();
                $end_date = Carbon::today()->addDay();
                $formatQuery = '%Y-%m-%d %H';
                $formatGroupBy = 'Y-m-d H';
                $increment = "addHour";
                $formatResponse = 'H';
            } else if ($request->fixRange == 'yearly') {
                $start_date = Carbon::today()->subYear();
                $end_date = Carbon::today()->addDay();
                $formatQuery = '%Y-%m';
                $formatGroupBy = 'Y-m';
                $increment = "addMonth";
                $formatResponse = 'Y-m';
            }
        }

        //query
        $ordersByDate = Order::groupBy(DB::raw("DATE_FORMAT(created_at, '$formatQuery')"))
            ->selectRaw("count(id) as count, DATE_FORMAT(created_at, '$formatQuery') as created_at_formatted, created_at")
            ->orderByDesc('created_at');
        if (isset($start_date) && isset($end_date)) {
            $ordersByDate = $ordersByDate->whereBetween('created_at', [$start_date->toDateString(), $end_date->toDateString()]);
        }
        $ordersByDate = $ordersByDate->get()
            ->groupBy(function ($date) use ($formatGroupBy) {
                return Carbon::parse($date->created_at)->format($formatGroupBy); // grouping by date
            });


        //create Response for each date
        $all_dates = array();
        while ($end_date->gte($start_date)) {
            if (isset($ordersByDate[$start_date->format($formatGroupBy)])) {
                $all_dates[$start_date->format($formatResponse)] = $ordersByDate[$start_date->format($formatGroupBy)][0]->count;
            } else {
                $all_dates[$start_date->format($formatResponse)] = 0;
            }
            $start_date->$increment();
        };

        //end Orders Chart

        //Total earnings widget

        $totalEarning = Order::whereNotNull('shipped_at')->selectRaw('sum(amount) as total')->first()->total;

        //end Total earnings widget

        //Orders this month widget

        $OrdersThisMonth = Order::whereBetween('created_at', [Carbon::create(Carbon::now()->year, Carbon::now()->month, 1)->toDateString(), Carbon::today()->addDay()->toDateString()])->selectRaw('count(id) as orders')->first()->orders;

        //end Orders this month widget

        //Potential earnings widget

        $potentialEarning = Order::whereNull('shipped_at')->selectRaw('sum(amount) as total')->first()->total;

        //end Potential earnings widget

        return response()->json(['orders' => $orders]);
    }


    /**
     * @OA\Get(
     *     tags={"Orders"},
     *     path="/api/v1/order/{uuid}/download",
     *     summary="Download an order",
     *     security={ {"bearer": {}} },
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/pdf",
     *             @OA\Schema(type="string",format="binary")
     *         )
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
        $order = Order::with('user', 'products_relation')->find($uuid);

        $customer = new Buyer([
            'name'          => $order->user->full_name,
            'custom_fields' => [
                'email' => $order->user->email,
            ],
        ]);

        $items = [];
        foreach ($order->products_relation as $product) {
            $quantity = array_filter($order->products, function ($el) use ($product) {
                return $el['uuid'] == $product->uuid;
            });
            $quantity = reset($quantity)['quantity'];
            $items[] = (new InvoiceItem())->title($product->title)
                ->description($product->description)
                ->pricePerUnit(floatVal($product->price))
                ->quantity($quantity);
        }

        $client = new Party([
            'name'          => 'Buckhill',
            'phone'         => '(555) 555-5555',
        ]);


        $invoice = Invoice::make('receipt')
            // ->series('BIG')

            // ->status(__('invoices::invoice.paid'))
            // ->sequence(667)
            // ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->seller($client)
            ->buyer($customer)
            ->date(new Carbon($order->created_at))
            ->dateFormat('m/d/Y')
            ->currencySymbol('$')
            ->currencyCode('USD')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename($order->uuid)
            ->addItems($items);
        // ->logo(public_path('vendor/invoices/sample-logo.png'))
        // You can additionally save generated invoice to configured disk
        return $invoice->download();
    }

    /**
     * @OA\Get(
     *     tags={"Orders"},
     *     path="/api/v1/orders/shipment-locator",
     *     summary="List all shipped orders",
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
     *         name="dateRange",
     *         in="query",
     *         @OA\Schema(
     *             type="object",
     *             default={"from": "string", "to": "string"}
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="fixRange",
     *         in="query",
     *         schema={"type": "string", "enum": {"today", "monthly", "yearly"}}
     *     ),
     *     @OA\Parameter(
     *         name="orderUuid",
     *         in="query",
     *         schema={"type": "string"}
     *     ),
     *     @OA\Parameter(
     *         name="customerUuid",
     *         in="query",
     *         schema={"type": "string"}
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

    public function shipmentLocator(request $request)
    {
        //handle dateRange param
        if ($request->has('dateRange')) {
            $range = json_decode($request->dateRange);
            // dd($range);
            if ($range->from && $range->to) {
                $start_date = new Carbon($range->from);
                $end_date = new Carbon($range->to);
            }
        }

        //handle fixRange param
        if ($request->has('fixRange')) {
            if ($request->fixRange == 'monthly') {
                $start_date = Carbon::today()->subMonth();
                $end_date = Carbon::today()->addDay();
            } else if ($request->fixRange == 'today') {
                $start_date = Carbon::today();
                $end_date = Carbon::today()->addDay();
            } else if ($request->fixRange == 'yearly') {
                $start_date = Carbon::today()->subYear();
                $end_date = Carbon::today()->addDay();
            }
        }
        $orders = Order::with('status', 'user', 'products_relation');

        if ($request->has('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $orders = $orders->orderBy($request->sortBy, 'desc');
            } else {
                $orders = $orders->orderBy($request->sortBy);
            }
        }
        if (isset($start_date) && isset($end_date)) {
            $orders = $orders->whereBetween('created_at', [$start_date->toDateString(), $end_date->toDateString()]);
        }
        if ($request->has('orderUuid')) {
            $orders = $orders->where('uuid', $request->orderUuid);
        }
        if ($request->has('customerUuid')) {
            $orders = $orders->where('user_id', $request->customerUuid);
        }
        $orders = $orders->whereNotNull('shipped_at')->paginate($request->limit ?? 10);
        //end orders list

        return response()->json($orders);
    }


        /**
     * @OA\Delete(
     *     tags={"Orders"},
     *     path="/api/v1/order/delete/{uuid}",
     *     summary="Delete Order",
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
        $order = Order::find($uuid);

        $order->delete();
    }
}
