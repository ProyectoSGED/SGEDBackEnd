<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        $domain = parse_url($_SERVER['HTTP_REFERER']);
        $host = "*";

        if (isset($domain['host'])) {
            $host = $domain['host'];
        }

        $response = $next($request);

        $response->header('Access-Control-Allow-Origin', $host);
        $response->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application, Origin, X-Auth-Token');
        $response->header('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
