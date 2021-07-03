<?php

namespace Fluent\Orm\Casts;

use CodeIgniter\Config\Services;
use Fluent\Orm\Contracts\Castable;
use Fluent\Orm\Contracts\CastsAttributes;

class AsEncryptedArrayObject implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return object|string
     */
    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
        {
            public function get($model, $key, $value, $attributes)
            {
                return new ArrayObject(json_decode(Services::encrypter()->decrypt($attributes[$key]), true));
            }

            public function set($model, $key, $value, $attributes)
            {
                return [$key => Services::encrypter()->encrypt(json_encode($value))];
            }

            public function serialize($model, string $key, $value, array $attributes)
            {
                return $value->getArrayCopy();
            }
        };
    }
}
