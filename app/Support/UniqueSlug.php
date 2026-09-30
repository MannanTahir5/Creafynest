<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class UniqueSlug
{
    public static function for(string $table, string $title, ?string $desired = null, ?int $ignoreId = null): string
    {
        $base = Str::slug($desired ?: $title) ?: 'item';
        $slug = $base;
        $i = 2;

        while (DB::table($table)
            ->where('slug', $slug)
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
