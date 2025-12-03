<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStoreIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user || ! $user->sellerStore || $user->sellerStore->status !== 'active') {
            abort(403, 'Store Anda tidak aktif.');
        }

        return $next($request);
    }
}
