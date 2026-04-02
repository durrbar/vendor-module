<?php

declare(strict_types=1);

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Table('commissions')]
#[Unguarded]
class Commission extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'image' => 'json',
        ];
    }
}
