<?php

namespace App\Services;

use Carbon\Carbon;

class ValidationService {

    /**
     * This will try to verify validity of an object based on start and enddate strings.
     * A begindate with no enddate should return true. The object is still valid.
     * If there is an enddate and it is in the past, the object is not valid.
     * If the enddate is in the future, validity will expire. (But is still valid)
     * etc. etc.
     * @param string $beginDate
     * @param string $endDate
     * @return bool
     */
    public function checkBagBeginEndDate(?string $beginDate, ?string $endDate): bool
    {
        $beginDate = $beginDate ? Carbon::parse($beginDate): null;
        $endDate = $endDate ? Carbon::parse($endDate): null;

        $valid = match (true) {
            empty($beginDate) && empty($endDate) => false,
            !empty($beginDate) && empty($endDate) => true,
            (empty($beginDate) && !empty($endDate)) && $endDate->isFuture() => true,
            empty($beginDate) && !empty($endDate) => false,
            $beginDate && $beginDate->isFuture() => false,
            $endDate->isFuture() => true,
            $endDate->isPast() => false,
            default => true
        };

        return $valid;
    }

}
