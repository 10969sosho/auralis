<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogging
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $auditableActions = [
            'login', 'logout', 'booking.store', 'booking.payment',
            'booking.refund', 'boarding.scan', 'schedule.store',
            'promo.store',
        ];

        $routeName = $request->route()?->getName();

        if (in_array($routeName, $auditableActions) && Auth::check()) {
            AuditLog::log(
                $routeName,
                $request->route()?->parameterNames()[0] ?? 'system',
                $request->route()?->parameter('id') ?? $request->route()?->parameter('schedule'),
                ['method' => $request->method(), 'url' => $request->fullUrl()],
                Auth::id(),
                $request->ip(),
            );
        }

        return $response;
    }
}
