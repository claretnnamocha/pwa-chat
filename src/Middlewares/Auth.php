<?php
namespace Alpha\Middlewares;

use MiladRahimi\PhpRouter\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Closure;

class Auth implements Middleware
{
    public function handle(ServerRequestInterface $request, Closure $next)
    {	
    	if (!session_get('signed-in')) {    
        	redirect(url('setup'));
    	}
    	return $next($request);
    }
}