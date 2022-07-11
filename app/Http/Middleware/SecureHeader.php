<?php

namespace App\Http\Middleware;

use Closure;

class SecureHeader
{

    protected $unwantedHeaderList = [

        'Server',
        'X-Powered-By'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->removeUnwantedHeaders($this->unwantedHeaderList);
        $response = $next($request);
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Accept, Content-Type, Authorization');
        $response->headers->set('Access-Control-Allow-Methods', 'PUT, POST, GET, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Expose-Headers', '*');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        return $response;
    }

    /**
     * Remove unwanted headers.
     *
     * @param  Array  $headerList
     */
    protected function removeUnwantedHeaders($headerList)
    {
        foreach ($headerList as $header)
            header_remove($header);
    }
}
