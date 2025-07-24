<?php

namespace App\Models;

use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as Authenticatable;
use Orchid\Filters\Filterable;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPays;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public function isAdmin(): bool
    {
        return $this->group === 'admin';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'firstname',
        'lastname',
        'email',
        'password',
        'permissions',
        'action',
        'phone',
        'email_verified_at',
	    'auto',
	    'psy_lance',
	    'used_sub'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions'          => 'array',
        'email_verified_at'    => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
           'id'         => Where::class,
           'firstname'       => Like::class,
              'lastname'    => Like::class,
           'email'      => Like::class,
           'updated_at' => WhereDateStartEnd::class,
           'created_at' => WhereDateStartEnd::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'firstname',
        'lastname',
        'phone',
        'email',
        'updated_at',
        'created_at',
        'subscription_level'
    ];

    /**
     * Get the user's active subscription.
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'user_id');
    }

    /**
     * Get the user's introduction.
     */
    public function introduction()
    {
        return $this->hasOne(\App\Models\Introduction::class, 'email', 'email');
    }

    /**
     * Get the user's transactions.
     */
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transactions::class, 'user_id');
    }

    /**
     * Get the user's subscription payments.
     */
    public function subscriptionPays()
    {
        return $this->hasMany(\App\Models\SubscriptionPays::class, 'user_id');
    }

    /**
     * Get the user's participant actions.
     */
    public function participantActions()
    {
        return $this->hasMany(\App\Models\ParticipantActions::class, 'user_id');
    }

    public function userPhoneVerified()
    {
        return !is_null($this->phone_verified_at);
    }

    public function phoneVerifiedAt()
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function getSubscriptionProductId(): ?int
    {
        if (!$this->subscription) {
            return null;
        }

        // Priority 1: Look up product by level from the subscription
        if (isset($this->subscription->level)) {
            $product = Product::where('level', $this->subscription->level)->first();
            if ($product) {
                return $product->id;
            }
        }

        // Priority 2: Fallback to finding the product from the last successful payment
        $lastPayment = SubscriptionPays::where('user_id', $this->id)
            ->where('active', 1) // Successful payment
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastPayment ? $lastPayment->product_id : null;
    }

    /**
     * Check if the user has a specific named subscription.
     *
     * @param string|array $names The name(s) of the subscription from config/subscriptions.php (e.g., 'premium', or ['premium', 'basic'])
     * @return bool
     */
    public function hasSubscription($names): bool
    {
        $productId = $this->getSubscriptionProductId();

        if (!$productId) {
            return false;
        }

        $names = is_array($names) ? $names : [$names];

        foreach ($names as $name) {
            $configId = config("subscriptions.products.{$name}.id");
            if ($configId && $productId == $configId) {
                return true;
            }
        }

        return false;
    }
}
