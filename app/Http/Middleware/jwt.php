<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use App\Models\User;
use Exception;
use InvalidArgumentException;

class jwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $config = Configuration::forAsymmetricSigner(
            // You may use RSA or ECDSA and all their variations (256, 384, and 512) and EdDSA over Curve25519
            new Signer\Rsa\Sha256(),
            InMemory::file(base_path() . '/privateKey.pem'),
            InMemory::file(base_path() . '/publicKey.pem')
            // You may also override the JOSE encoder/decoder if needed by providing extra arguments here
        );

        $bearer = $request->bearerToken() ?? '';

        try{
            $token = $config->parser()->parse(
                $bearer
            );
        } catch(InvalidArgumentException $e){
            return response()->json('Invalid Token', 401);
        }
        
        if($token instanceof UnencryptedToken) {

        $uuid = $token->claims()->get('user_uuid');

        $user = User::where('uuid', $uuid)->first();
        if ($user) {
            $request->session()->put('user', $user);
            return $next($request);
        }
        
        }

        return response()->json('Invalid Token', 401);
    }
}
