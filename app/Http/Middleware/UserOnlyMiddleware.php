<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserOnlyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        if ($user->is_admin == 1 || $user->is_admin === true) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Access denied. This area is for regular users only.',
                    'redirect' => route('admin.dashboard')
                ], 403);
            }
            return redirect()->route('admin.dashboard')
                ->with('warning', 'You are logged in as admin. Use the admin panel.');
        }

        return $next($request);
    }
}
