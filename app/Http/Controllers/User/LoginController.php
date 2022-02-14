<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;

class LoginController extends Controller
{
    public function login()
    {
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
                        ->withClaim('user_uuid', 1)
                        // Configures a new header, called "foo"
                        // Builds a new token
                        ->getToken($configuration->signer(), $configuration->signingKey());

        return response()->json($token->toString());
    }
}
