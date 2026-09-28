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
            'login', 'login.post', 'logout', 'booking.store', 'booking.process-payment',
            'booking.refund', 'boarding.scan', 'counter.store', 'counter.refund',
            'deportation.booking.store', 'deportation.payment.process',
            'deportation.manifests.store', 'deportation.passengers.store',
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
