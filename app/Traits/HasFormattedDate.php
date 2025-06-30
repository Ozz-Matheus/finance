<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasFormattedDate
{
    public function getFormattedDateAttribute(): string
    {
        if (! $this->date) {
            return '';
        }

        return ucfirst(Carbon::parse($this->date)->translatedFormat('F \d\e Y'));
    }
}
