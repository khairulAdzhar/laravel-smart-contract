<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAuthenticated
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
        if (!Auth::check()) {
            return redirect('google.com');
        }

        if (Auth::user()->is_gpt === 0) {
            return redirect(route('redirectSmartContractOrg'));
        }
        if (Auth::user()->is_smart_contract_status === 0) {
            return redirect(route('redirectSmartContractOrg'));

        }

        return $next($request);
    }
}
