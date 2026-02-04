<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login', ['admin' => '1']);
        }

        if ($user->is_admin != 1 && $user->is_admin !== true) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Access denied. Admin privileges required.',
                    'redirect' => route('dashboard')
                ], 403);
            }
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. You need administrator privileges.');
        }

        return $next($request);
    }
}
