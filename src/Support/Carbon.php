<?php

namespace Fluent\Orm\Support;

use Carbon\Carbon as BaseCarbon;
use Carbon\CarbonImmutable as BaseCarbonImmutable;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use CodeIgniter\Config\Services;

class Carbon extends BaseCarbon
{
    /**
     * Create a new Carbon instance.
     *
     * Please see the testing aids section (specifically static::setTestNow())
     * for more on the possibility of this constructor returning a test instance.
     *
     * @param DateTimeInterface|string|null $time
     * @param DateTimeZone|string|null      $tz
     *
     * @throws InvalidFormatException
     */
    public function __construct($time = null, $tz = null)
    {
        $locale = Services::request()->getLocale();

        BaseCarbon::setLocale($locale);
        BaseCarbonImmutable::setLocale($locale);
        CarbonPeriod::setLocale($locale);
        CarbonInterval::setLocale($locale);

        parent::__construct($time, $tz);
    }

    /**
     * {@inheritdoc}
     */
    public static function setTestNow($testNow = null)
    {
        BaseCarbon::setTestNow($testNow);
        BaseCarbonImmutable::setTestNow($testNow);
    }
}
