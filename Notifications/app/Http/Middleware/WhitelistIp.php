<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WhitelistIp
{
    /**
     * @var array contains all the IP addresses that are allowed to access
     * the notification service resources. and blocks any other requests made outstide
     * network.
     */
    private $whitelist = [
        '127.0.0.1',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!in_array($request->getClientIp(), $this->whitelist)){
            return response(['Message' => 'Access denied.'], 403);
        }
        return $next($request);
    }
}
