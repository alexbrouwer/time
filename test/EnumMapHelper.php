<?php

namespace PARTest\Time;

use PAR\Core\Helper\InstanceHelper;
use PAR\Enum\Enum;
use PAR\Enum\EnumMap;

abstract class EnumMapHelper
{
    protected static $keyType;

    public static function createMap(string $valueType, bool $allowNullValues = false): EnumMap
    {
        return EnumMap::for(static::$keyType, $valueType, $allowNullValues);
    }

    public static function createMapWithAll(string $valueType, $defaultValue, bool $allowNullValues = false): EnumMap
    {
        $map = static::createMap($valueType, $allowNullValues);

        if ($allowNullValues || $defaultValue !== null) {
            foreach (static::values() as $field) {
                $map->put($field, $defaultValue);
            }
        }

        return $map;
    }

    public static function supported(array $supported): EnumMap
    {
        $map = static::createMap('bool');

        return static::updateAllIn($map, $supported, true);
    }

    public static function toProviderArray(EnumMap $map): array
    {
        $data = [];

        /**
         * @var Enum  $enum
         * @var mixed $value
         */
        foreach ($map as $enum => $value) {
            $result = $value;
            if (!is_array($result)) {
                $result = [$result];
            }

            array_unshift($result, $enum);

            $data[$enum->toString()] = $result;
        }

        return $data;
    }

    public static function unsupported(array $supported): EnumMap
    {
        $map = static::createMap('bool');

        return static::updateAllNotIn($map, $supported, false);
    }

    public static function updateAllIn(EnumMap $map, array $list, $value): EnumMap
    {
        foreach (static::values() as $field) {
            if (InstanceHelper::isAnyOf($field, $list)) {
                $map->put($field, $value);
            }
        }

        return $map;
    }

    public static function updateAllNotIn(EnumMap $map, array $list, $value): EnumMap
    {
        foreach (static::values() as $field) {
            if (!InstanceHelper::isAnyOf($field, $list)) {
                $map->put($field, $value);
            }
        }

        return $map;
    }

    private static function values(): array
    {
        return forward_static_call([static::$keyType, 'values']);
    }
}
