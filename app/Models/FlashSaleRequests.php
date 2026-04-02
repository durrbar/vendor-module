<?php

declare(strict_types=1);

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Ecommerce\Models\Product;

#[Table('flash_sale_requests')]
#[Unguarded]
class FlashSaleRequests extends Model
{
    use HasUuids;
    use SoftDeletes;

    public function flash_sale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class, 'flash_sale_id');
    }

    /**
     * products
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'flash_sale_requests_products');
    }
}
