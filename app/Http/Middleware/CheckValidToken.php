<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\CustomResponse;
use Closure;
use JWTAuth;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Laravel\Passport\TokenRepository;


class CheckValidToken
{
    use CustomResponse;
    public function handle($request, Closure $next)
    {
        $accessTokenRepository = new TokenRepository;

        //將原先的request轉為psr格式
        try {
            $psr = (new DiactorosFactory)->createRequest($request);
        } catch (\Exception $e) {
            throw $e;
        }

        if ($psr->hasHeader('Authorization') === false) {
            return $this->CustomResponse(401, 'without authorization header');
        }

        $header = $psr->getHeader('Authorization');
        $jwt = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $header[0]));

        try {
            // Attempt to parse and validate the JWT
            $publicKey = file_get_contents(storage_path('oauth-public.key'));
            if (substr_count($jwt, '.') < 2) {
                return $this->CustomResponse(401, 'bearer token format error');
            }
            $token = (new Parser())->parse($jwt);
            if ($token->verify(new Sha256(),$publicKey ) === false) {
                return $this->CustomResponse(401, 'token key verify fail');
            }

            // Ensure access token hasn't expired
            $data = new ValidationData();
            $data->setCurrentTime(time());

            if ($token->validate($data) === false) {
                return $this->CustomResponse(401, 'access token expired');
            }

            // Check if token has been revoked
            if ($accessTokenRepository->isAccessTokenRevoked($token->getClaim('jti'))) {
                return $this->CustomResponse(401, 'access token revoked');
            }
            $user = User::find($token->getClaim('sub'));
        } catch (\Exception $exception) {
            throw $exception;
        }
        $request->attributes->add(['user'=> $user]);
        return $next($request);
    }


}
