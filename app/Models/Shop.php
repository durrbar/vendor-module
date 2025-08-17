<?php

namespace Modules\Vendor\Models;

use Attribute;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Chat\Models\Conversation;
use Modules\Coupon\Models\Coupon;
use Modules\Ecommerce\Models\Category;
use Modules\Ecommerce\Models\Faqs;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Models\TermsAndConditions;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

class Shop extends Model
{
    use HasUuids;
    use Sluggable;

    protected $table = 'shops';

    public $guarded = [];

    protected $casts = [
        'logo' => 'json',
        'cover_image' => 'json',
        'address' => 'json',
        'settings' => 'json',
    ];

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function balance(): HasOne
    {
        return $this->hasOne(Balance::class, 'shop_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shop_id');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class, 'shop_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'shop_id');
    }

    public function withdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class, 'shop_id');
    }

    public function staffs(): HasMany
    {
        return $this->hasMany(User::class, 'shop_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(User::class, 'shop_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_shop');
    }

    public function users(): BelongsToMany
    {
        return $this->BelongsToMany(User::class, 'user_shop');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'shop_id');
    }

    /**
     * faqs
     */
    public function faqs(): HasMany
    {
        return $this->HasMany(Faqs::class);
    }

    /**
     * terms and conditions
     */
    public function terms_and_conditions(): HasMany
    {
        return $this->HasMany(TermsAndConditions::class);
    }

    /**
     * faqs
     */
    public function coupons(): HasMany
    {
        return $this->HasMany(Coupon::class);
    }

    /**
     * ownership transfers
     */
    public function ownership_history(): HasOne
    {
        return $this->hasOne(OwnershipTransfer::class, 'shop_id');
    }
}
