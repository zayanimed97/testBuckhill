<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;

class UserController extends Controller
{
        /**
     * @OA\Post(
     *     tags={"user"},
     *     path="/api/v1/user/create",
     *     summary="Create a user",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"first_name","last_name","email","password","password_confirmation","address","phone_number"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="first_name",
     *                     description="User first name",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="last_name",
     *                     description="User last name",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="email",
     *                     description="User Email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     description="User password confirmation",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="avatar",
     *                     description="User Avatar image UUID",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     description="User address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     description="User phone number",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="is_marketing",
     *                     description="User marketing preferences",
     *                     type="string"
     *                 ),
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email:rfc,dns',
            'password' => 'required|confirmed',
            'address' => 'required',
            'phone_number' => 'required',
        ]);
        if (!$validated->fails()) {
            $user = new User();
            $user->uuid = (string) Str::uuid();
            $user->first_name = $request['first_name'];
            $user->last_name = $request['last_name'];
            $user->email = $request['email'];
            $user->password = bcrypt($request['password']);
            $user->avatar = $request['avatar'] ?? '';
            $user->address = $request['address'];
            $user->phone_number = $request['phone_number'];
            $user->is_admin = 0;
            $user->is_marketing = $request['is_marketing'] ?? '0';
            $user->save();
        }

        return response()->json($validated->errors());
    }

            /**
     * @OA\Put(
     *     tags={"user"},
     *     path="/api/v1/user/update",
     *     summary="Update a user",
     *     security={ {"bearer": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"first_name","last_name","email","password","password_confirmation","address","phone_number"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="first_name",
     *                     description="User first name",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="last_name",
     *                     description="User last name",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="email",
     *                     description="User Email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     description="User password confirmation",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="avatar",
     *                     description="User Avatar image UUID",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     description="User address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     description="User phone number",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="is_marketing",
     *                     description="User marketing preferences",
     *                     type="string"
     *                 ),
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
    public function update(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email:rfc,dns',
            'password' => 'required|confirmed',
            'address' => 'required',
            'phone_number' => 'required',
        ]);
        if (!$validated->fails()) {
            $user = User::find($request->session()->get('user')['id']);
            $user->first_name = $request['first_name'];
            $user->last_name = $request['last_name'];
            $user->email = $request['email'];
            $user->password = bcrypt($request['password']);
            $user->avatar = $request['avatar'] ?? '';
            $user->address = $request['address'];
            $user->phone_number = $request['phone_number'];
            $user->is_marketing = $request['is_marketing'] ?? '0';
            $user->update();
        }

        return response()->json($validated->errors());
    }

    /**
     * @OA\Delete(
     *     tags={"user"},
     *     path="/api/v1/user/delete",
     *     summary="Delete User",
     *     security={ {"bearer": {}} },
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
    public function delete(Request $request)
    {
        $user = User::find($request->session()->get('user')['id']);

        $user->delete();

        return response()->json([]);
    }

            /**
     * @OA\Post(
     *     tags={"user"},
     *     path="/api/v1/user/login",
     *     summary="Login an user account",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"email","password"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="email",
     *                     description="User email",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="password",
     *                     description="User password",
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
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->where('is_admin', '0')->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)){
                $configuration = Configuration::forAsymmetricSigner(
                    // You may use RSA or ECDSA and all their variations (256, 384, and 512) and EdDSA over Curve25519
                    new Signer\Rsa\Sha256(),
                    InMemory::file(base_path() . '/privateKey.pem'),
                    InMemory::file(base_path() . '/publicKey.pem')
                    // You may also override the JOSE encoder/decoder if needed by providing extra arguments here
                );
        
                $now   = new \DateTimeImmutable();
                $token = $configuration->builder()
                                // Configures the issuer (iss claim)
                                ->issuedBy(url(''))
                                // Configures the time that the token was issue (iat claim)
                                ->issuedAt($now)
                                // Configures the expiration time of the token (exp claim)
                                ->expiresAt($now->modify('+1 day'))
                                // Configures a new claim, called "uid"
                                ->withClaim('user_uuid', $user->uuid)
                                // Configures a new header, called "foo"
                                // Builds a new token
                                ->getToken($configuration->signer(), $configuration->signingKey());
        
                return response()->json($token->toString());
            } else {
                return response()->json('Incorrect Password', 422);
            }
        } else {
            return response()->json('Incorrect Email', 422);
        }
    }

            /**
     * @OA\Post(
     *     tags={"user"},
     *     path="/api/v1/user/forgot-password",
     *     summary="Create a token to reset a user password",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"email"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="email",
     *                     description="User email",
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
    public function forgot_password(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
        ]);
        if(!$validated->fails()){
            $user = User::where('email', $request->email)->where('is_admin', '0')->first();
            if ($user) {
                $token = Str::random(128);

                PasswordReset::updateOrCreate(
                    [
                        'email'   => $request->email,
                    ],
                    [
                        'token' => $token,
                        'created_at' => now()
                    ],
                );

                return response()->json(["message" => 'Please use this token to reset your password', 'token'=> $token]);
            } else {
                return response()->json('Incorrect Email', 422);
            }
        }
    }

                /**
     * @OA\Post(
     *     tags={"user"},
     *     path="/api/v1/user/reset-password-token",
     *     summary="Reset a user password with the token",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"email","password","token","password_confirmation"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="token",
     *                     description="User reset token",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="email",
     *                     description="User email",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="password_confirmation",
     *                     description="User password",
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
    public function reset_password_token(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'password' => 'required|confirmed',
            'token' => 'required',
        ]);

        if (!$validated->fails()) {
            $token = PasswordReset::where('email',$request->email)->where('token', $request->token)->first();
            if ($token) {
                User::where('email', $request->email)->update(['password'=>bcrypt($request->password)]);
                $token->delete();
            }
        }
        return response()->json($validated->errors());
    }
        /**
     * @OA\Get(
     *     tags={"user"},
     *     path="/api/v1/user/orders",
     *     summary="List all users",
     *     security={ {"bearer": {}} },
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
        $orders = Order::where('user_id', $request->session()->get('user')['id']);

        if ($request->filled('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $orders = $orders->orderBy($request->sortBy, 'desc');
            } else {
                $orders = $orders->orderBy($request->sortBy);
            }
        }

        $orders = $orders->paginate($request->limit ?? 10);

        

        return response()->json($orders);
    }

}
