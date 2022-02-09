<?php

namespace HDVinnie\Purifier\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CleanHtmlInput implements CastsAttributes
{
    /**
     * Cast the given value. Does not clean the HTML.
     */
    public function get(\Illuminate\Database\Eloquent\Model $model, string $key, $value, array $attributes): array
    {
        return $value;
    }

    /**
     * Prepare the given value for storage by cleaning the HTML.
     */
    public function set(\Illuminate\Database\Eloquent\Model $model, string $key, array $value, array $attributes)
    {
        return clean($value);
    }
}
