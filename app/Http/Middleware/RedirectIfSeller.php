<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfSeller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ($user->is_seller || \App\Models\SellerStore::where('user_id', $user->id)->exists())) {
            return redirect()->route('seller.dashboard')->with('message', 'Anda sudah memiliki toko.');
        }
        return $next($request);
    }
}
