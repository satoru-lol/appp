<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Subscription;

class EnsureSubscriptionIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $subscription = $user->subscription;

            // Check if subscription status is potentially incorrect (e.g., level 0)
            if ($subscription && $subscription->level == 0) {

                // Use the logic from the User model to find the correct product ID
                $correctProductId = $user->getSubscriptionProductId();

                if ($correctProductId) {
                    $product = Product::find($correctProductId);
                    
                    if ($product) {
                        $needsUpdate = false;
                        if ($subscription->level != $product->level) {
                            $subscription->level = $product->level;
                            $needsUpdate = true;
                        }

                        if (!$subscription->is_active) {
                            $subscription->is_active = 1;
                            $needsUpdate = true;
                        }

                        if ($needsUpdate) {
                            $subscription->save();
                            // Reload the user's subscription relationship to reflect the change immediately
                            $user->load('subscription');
                        }
                    }
                }
            }
        }

        return $next($request);
    }
} 