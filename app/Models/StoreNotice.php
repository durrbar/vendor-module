<?php

declare(strict_types=1);

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Role\Enums\Permission;
use Modules\User\Models\User;
use Modules\Vendor\Observers\StoreNoticeObserver;

#[ObservedBy([StoreNoticeObserver::class])]
#[Table('store_notices')]
#[Unguarded]
#[Appends(['is_read', 'creator_role'])]
class StoreNotice extends Model
{
    use HasUlids;
    use SoftDeletes;

    public $with = ['creator', 'users', 'shops', 'read_status'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function read_status(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'store_notice_read')->withPivot('is_read');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'store_notice_user');
    }

    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'store_notice_shop');
    }

    public function getCreatorRoleAttribute(): string
    {
        $creator = $this->relationLoaded('creator')
            ? $this->getRelation('creator')
            : $this->creator()->with('permissions')->first();

        if (! $creator) {
            return '';
        }

        $permissions = $creator->relationLoaded('permissions')
            ? $creator->getRelation('permissions')
            : $creator->permissions()->get();

        $permissionArr = $permissions->pluck('name')->toArray();

        if (in_array(Permission::SuperAdmin->value, $permissionArr)) {
            return ucfirst(str_replace('_', ' ', Permission::SuperAdmin->value));
        }

        return ucfirst(str_replace('_', ' ', Permission::StoreOwner->value));
    }

    public function getIsReadAttribute(): bool
    {
        $authenticatedUserId = Auth::id();

        if (! $authenticatedUserId) {
            return false;
        }

        if ($this->relationLoaded('read_status')) {
            foreach ($this->getRelation('read_status') as $readStatus) {
                if ($readStatus->id === $authenticatedUserId && $readStatus->pivot?->is_read) {
                    return true;
                }
            }

            return false;
        }

        return $this->read_status()
            ->where('users.id', $authenticatedUserId)
            ->wherePivot('is_read', true)
            ->exists();
    }
}
