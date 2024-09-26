<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetSchemaMiddleware
{
    protected DB $dbFacade;

    public function __construct(DB $dbFacade)
    {
        $this->dbFacade = $dbFacade;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->session()->get('user_id');
        if ($userId) {
            //            $schemaName = 'user_'.$userId;
            $this->dbFacade::statement('SET search_path TO gilberto');
        }

        return $next($request);
    }
}
