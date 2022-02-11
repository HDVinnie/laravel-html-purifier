<?php

namespace HDVinnie\Purifier\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CleanHtmlOutput implements CastsAttributes
{
    /**
     * Clean the HTML when casting the given value.
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return clean($value);
    }

    /**
     * Prepare the given value for storage. Does not clean the HTML.
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}
