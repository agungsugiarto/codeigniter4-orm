<?php

namespace Fluent\Orm\Config;

use CodeIgniter\Config\BaseService;
use Faker\Factory as FakerFactory;
use Fluent\Orm\Factory as OrmFactory;

class Services extends BaseService
{
    /**
     * The array of resolved Faker instances.
     *
     * @var array
     */
    protected static $fakers = [];

    /**
     * Service faker instance.
     *
     * @param string $locale
     * @param bool $getShared
     * @return \Faker\Generator
     */
    public static function faker($locale = 'en_US', bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('faker', $locale);
        }

        if (! isset(static::$fakers[$locale])) {
            static::$fakers[$locale] = FakerFactory::create($locale);
        }

        static::$fakers[$locale]->unique(true);

        return static::$fakers[$locale];
    }

    /**
     * Service orm factory instance.
     *
     * @param string $pathToFactories
     * @param string $locale
     * @param bool $getShared
     * @return OrmFactory
     */
    public static function factory($pathToFactories = APPPATH . 'Database/Factories', $locale = 'en_US', bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('factory', $pathToFactories, $locale);
        }

        return OrmFactory::construct(static::faker($locale, $getShared), $pathToFactories);
    }
}
