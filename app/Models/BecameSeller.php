<?php

declare(strict_types=1);

namespace Modules\Vendor\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Table('became_sellers')]
#[Unguarded]
class BecameSeller extends Model
{
    use HasUuids;

    public static function getData($language = DEFAULT_LANGUAGE)
    {
        $data = self::where('language', $language)->first();

        if (! $data) {
            $data = self::where('language', DEFAULT_LANGUAGE)->first();
        }

        return $data;
    }

    protected function casts(): array
    {
        return [
            'page_options' => 'json',
        ];
    }
}
