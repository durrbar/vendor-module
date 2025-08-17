<?php

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Modules\Role\Enums\Permission;
use Modules\User\Models\User;

class StoreNotice extends Model
{
    use HasUlids;
    use SoftDeletes;

    public $guarded = [];

    public $with = ['creator', 'users', 'shops', 'read_status'];

    protected $table = 'store_notices';

    protected $appends = [
        'is_read', 'creator_role',
    ];

    /**
     * parent boot menu from parent model
     */
    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($storeNotice): void {
            $storeNotice->created_by = Auth::id();
        });
        static::updating(function ($storeNotice): void {
            $storeNotice->updated_by = Auth::id();
        });
    }

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
        try {
            $permissionArr = $this->creator->permissions->pluck('name')->toArray();
            if (in_array(Permission::SUPER_ADMIN, $permissionArr)) {
                return ucfirst(str_replace('_', ' ', Permission::SUPER_ADMIN));
            }

            return ucfirst(str_replace('_', ' ', Permission::STORE_OWNER));
        } catch (\Throwable $th) {
            return '';
        }
    }

    public function getIsReadAttribute(): bool
    {
        try {
            $readStatusArr = $this->read_status;
            foreach ($readStatusArr as $readStatus) {
                if ($readStatus->id === Auth::id() && $readStatus->pivot->is_read) {
                    return true;
                }
            }

            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
