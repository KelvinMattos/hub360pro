<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceUtf8
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Força o cabeçalho Content-Type para UTF-8
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        
        return $response;
    }
}