<?php

declare(strict_types=1);

namespace Modules\Vendor\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Traits\TranslationTrait;

#[Table('flash_sales')]
#[Unguarded]
#[Appends(['translated_languages'])]
class FlashSale extends Model
{
    use HasUuids;
    use Sluggable;
    use SoftDeletes;
    use TranslationTrait;

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    #[Scope]
    public function withUniqueSlugConstraints(Builder $query, Model $model): Builder
    {
        return $query->where('language', $model->language);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'flash_sale_products')->withPivot('flash_sale_id', 'product_id');
    }

    public function flashSaleRequests(): HasMany
    {
        return $this->hasMany(FlashSaleRequests::class);
    }

    protected function casts(): array
    {
        return [
            'cover_image' => 'json',
            'sale_builder' => 'json',
            'image' => 'json',
        ];
    }
}
