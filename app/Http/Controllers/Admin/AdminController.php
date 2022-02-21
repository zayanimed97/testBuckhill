<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;

class AdminController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"admin"},
     *     path="/api/v1/admin/create",
     *     summary="Create an admin",
     *     security={ {"bearer": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"first_name","last_name","email","password","password_confirmation","avatar","address","phone_number"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="first_name",
     *                     description="Admin first name",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="last_name",
     *                     description="Admin last name",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="email",
     *                     description="Admin Email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Admin password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     description="Admin password confirmation",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="avatar",
     *                     description="Admin Avatar image UUID",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     description="Admin address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     description="Admin phone number",
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
            'avatar' => 'required',
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
            $user->avatar = $request['avatar'];
            $user->address = $request['address'];
            $user->phone_number = $request['phone_number'];
            $user->is_admin = 1;
            $user->is_marketing = 0;
            $user->save();
        }

        return response()->json($validated->errors());
    }

        /**
     * @OA\Get(
     *     tags={"admin"},
     *     path="/api/v1/admin/user-listing",
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
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="created_at",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="marketing",
     *         in="query",
     *         description="",
     *         schema={"type": "string", "enum": {"0", "1"}}
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
    public function user_listing(Request $request)
    {
        $user = User::where('is_admin', '0');

        foreach ($request->except(['limit', 'page', 'sortBy', 'desc']) as $field => $filter) {
            $user = $user->where($field, $filter);
        }

        if ($request->filled('sortBy')) {
            if (($request->desc ?? false) == 'true') {
                $user = $user->orderBy($request->sortBy, 'desc');
            } else {
                $user = $user->orderBy($request->sortBy);
            }
        }

        $user = $user->paginate($request->limit ?? 10);

        

        return response()->json($user);
    }

        /**
     * @OA\Post(
     *     tags={"admin"},
     *     path="/api/v1/admin/login",
     *     summary="Login an admin account",
     *     security={ {"bearer": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={"email","password"},
     *                  type="object",
     *                  @OA\Property(
     *                     property="email",
     *                     description="Admin email",
     *                     type="string"
     *                  ),
     *                  @OA\Property(
     *                     property="password",
     *                     description="Admin password",
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
        $user = User::where('email', $request->email)->where('is_admin', '1')->first();
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
}
