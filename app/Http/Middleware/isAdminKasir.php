<?php

namespace App\Http\Middleware;

use App\Model\SingleSignOn\PaymentBengkel;
use Closure;
use Illuminate\Support\Facades\Auth;

class isAdminKasir
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $payment_bengkel = PaymentBengkel::where('id_bengkel', Auth::user()->bengkel->id_bengkel)->orderBy('id_payment_bengkel', 'DESC')->first();
        if (Auth::user() && Auth::user()->hasRole('Aplikasi Point of Sales') || Auth::user()->role == 'owner' && $payment_bengkel->status == 'lunas') {
            return $next($request);
        }
        abort(403);
    }
}
