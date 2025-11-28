<?php

namespace App\Support;

class Access
{
    public static function can($permission)
    {
        $user = auth()->user();

        return $user && $user->can($permission);
    }

    public static function any(array $permissions)
    {
        foreach ($permissions as $perm) {
            if (self::can($perm)) {
                return true;
            }
        }

        return false;
    }
}
