<?php

namespace PAR\Time;

use PAR\Core\Helper\InstanceHelper;
use PAR\Enum\Enum;
use PAR\Enum\EnumMap;

final class EnumMapHelper
{
    /**
     * @param EnumMap     $map
     * @param array<Enum> $list
     * @param mixed       $value
     *
     * @return EnumMap
     */
    public static function putAll(EnumMap $map, array $list, $value): EnumMap
    {
        foreach ($list as $enum) {
            $map->put($enum, $value);
        }

        return $map;
    }

    /**
     * @param EnumMap     $map
     * @param array<Enum> $list
     *
     * @return EnumMap
     */
    public static function putAllFalse(EnumMap $map, array $list): EnumMap
    {
        return static::putAll($map, $list, false);
    }

    /**
     * @param EnumMap     $map
     * @param array<Enum> $list
     * @param mixed       $value
     *
     * @return EnumMap
     */
    public static function putAllIn(EnumMap $map, array $list, $value): EnumMap
    {
        if (empty($list)) {
            return $map;
        }

        $keyType = get_class(reset($list));
        /** @var callable $callable */
        $callable = [$keyType, 'values'];
        $all = $callable();

        /** @var array<Enum> $all */
        foreach ($all as $enum) {
            if (InstanceHelper::isAnyOf($enum, $list)) {
                $map->put($enum, $value);
            }
        }

        return $map;
    }

    /**
     * @param EnumMap     $map
     * @param array<Enum> $list
     * @param mixed       $value
     *
     * @return EnumMap
     */
    public static function putAllNotIn(EnumMap $map, array $list, $value): EnumMap
    {
        if (empty($list)) {
            return $map;
        }

        $keyType = get_class(reset($list));
        /** @var callable $callable */
        $callable = [$keyType, 'values'];
        $all = $callable();

        /** @var array<Enum> $all */
        foreach ($all as $enum) {
            if (!InstanceHelper::isAnyOf($enum, $list)) {
                $map->put($enum, $value);
            }
        }

        return $map;
    }

    /**
     * @param EnumMap     $map
     * @param array<Enum> $list
     *
     * @return EnumMap
     */
    public static function putAllTrue(EnumMap $map, array $list): EnumMap
    {
        return static::putAll($map, $list, true);
    }

    /**
     * @param EnumMap     $map
     * @param array<Enum> $list
     *
     * @return EnumMap
     */
    public static function putInTrue(EnumMap $map, array $list): EnumMap
    {
        return static::putAll($map, $list, true);
    }

    /**
     * @param EnumMap       $map
     *
     * @param callable|null $callable
     *
     * @return array<string, array>
     */
    public static function toProviderArray(EnumMap $map, ?callable $callable = null): array
    {
        $data = [];

        if (!$callable) {
            $callable = static function (Enum $enum, $value): array {
                return [$enum, $value];
            };
        }

        /**
         * @var Enum  $enum
         * @var mixed $value
         */
        foreach ($map as $enum => $value) {
            $result = $callable($enum, $value);
            if (!is_array($result)) {
                $result = [$enum, $result];
            }

            $data[$enum->toString()] = $result;
        }

        return $data;
    }
}
