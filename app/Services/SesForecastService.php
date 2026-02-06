<?php

namespace App\Services;

class SesForecastService
{
    public function forecastNext(array $actuals, float $alpha = 0.3)
    {
        $n = count($actuals);
        if ($n === 0) {
            return 0.0;
        }

        $s = $actuals[0];
        for ($t = 1; $t < $n; $t++) {
            $a = $actuals[$t];
            $s = ($alpha * $a) + ((1 - $alpha) * $s);
        }

        return $s;
    }
}
