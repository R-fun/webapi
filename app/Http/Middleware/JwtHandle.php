<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JwtHandle
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
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException){
                return response()->json([
                    'status'=>false,
                    'message'=>'Token Is Invalid'
                ],401);
            }else if ($e instanceof TokenInvalidException){
                return response()->json([
                    'status'=>false,
                    'message'=>'Token is Expired'
                ],401);
            }else{
                return response()->json([
                    'status'=>false,
                    'message'=>'Unauthorized or Authorization Token not found'
                ],401);
            }
        }
        // $response = $next($request);

        // return $response;
        // if (strpos($request->headers->get("Authorization"),"Bearer ") === false) {
        //     // if(!Auth::check()){
        //     // }else{
        //     // }
        // }

        $response = $next($request);

        return $response;
    }
}
