<?php

namespace app\http\middleware;

class Limit
{
    public function handle($request, \Closure $next)
    {
        $ip = $request->ip();
        $verify = config('base.auth.limit');
        if (in_array($ip, $verify)) {
            abort(404);
            exit;
        }
        return $next($request);
    }
}
