<?php

namespace App\Services;

class SesForecastService
{
    public function forecastNext(array $actuals, float $alpha = 0.3): ?float
    {
        $n = count($actuals);
        if ($n === 0) {
            return 0;
        }
        if ($n === 1) {
            return (float) $actuals[0];
        }

        $f = (float) $actuals[0];

        for ($t = 1; $t < $n; $t++) {
            $aPrev = (float) $actuals[$t - 1];
            $f = ($alpha * $aPrev) + ((1 - $alpha) * $f);
        }

        $aLast = (float) $actuals[$n - 1];
        $next = ($alpha * $aLast) + ((1 - $alpha) * $f);

        return $next;
    }
}
