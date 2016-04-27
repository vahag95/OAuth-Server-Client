<?php

namespace Api\Server\Http\Middleware;

use Closure;
use Api\Server\Services\OAuthService;
class OAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $oauthService = new OAuthService();
        $response = $oauthService->passOauth($request);
        if(isset($response['errors'])){
            return $response['errors'];
        }        
        return $next($request);
    }
}
