<?php

namespace app\http\middleware;

use app\helps\Rsa;

class Encrypt
{
    public function handle($request, \Closure $next)
    {
        $rsa = new Rsa();
        if ($request->isGet()) {
            $request->data = json_decode($rsa->privDecrypt($request->get('data')), true);
        }

        if ($request->isPost()) {
            $request->data = json_decode($rsa->privDecrypt($request->post('data')), true);
        }

        return $next($request);
    }


}
