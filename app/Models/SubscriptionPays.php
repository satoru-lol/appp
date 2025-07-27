<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPays extends Model
{
    use HasFactory;

    protected $table = "subscription_pays";

    protected $fillable = [
        "user_id",
        "subscription_id",
        "invoice_id",
        "product_id",
        "active",
        "test",
        "action",
        "price",
        "auto"
    ];

    /**
     * Get the product associated with the subscription payment.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the user associated with the subscription payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    /**
     * Get the state attribute for the subscription payment.
     *
     * This accessor allows us to have a unified 'state' field
     * across different types of transactions.
     *
     * @return string
     */
    public function getStateAttribute(): string
    {
        if ($this->action === 'cancel') {
            return 'cancelled';
        }

        return $this->active ? 'success' : 'pending';
    }
}
