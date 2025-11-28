<?php

namespace App\Support;

class ResourceResolver
{
    /**
     * Resolve default resource name from route.
     * Example: "item.index" → "item"
     */
    public static function resolve()
    {
        $route = request()->route()?->getName();

        if (! $route) {
            return null;
        }

        // ambil segmen sebelum titik pertama
        return explode('.', $route)[0];
    }

    /**
     * Get auto route.
     */
    public static function route($resource, $action, $params = [])
    {
        return route("$resource.$action", $params);
    }
}
