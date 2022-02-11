<?php

namespace HDVinnie\Purifier\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CleanHtmlInput implements CastsAttributes
{
    /**
     * Cast the given value. Does not clean the HTML.
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $value;
    }

    /**
     * Prepare the given value for storage by cleaning the HTML.
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return clean($value);
    }
}
