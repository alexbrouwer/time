<?php

namespace PARTest\Time;

use PAR\Enum\EnumMap;
use PAR\Time\Chrono\ChronoField;
use PAR\Time\Chrono\ChronoUnit;

final class SupportedProvider
{
    public static function fields(array $supportedFields): array
    {
        $fields = EnumMap::for(ChronoField::class, 'bool', false);

        foreach (ChronoField::values() as $field) {
            $fields->put($field, false);
        }

        foreach ($supportedFields as $field) {
            $fields->put($field, true);
        }

        $data = [];

        foreach ($fields as $field => $supported) {
            /** @var $field ChronoField */
            $data[$field->toString()] = [$field, $supported];
        }

        return $data;
    }

    public static function units(array $supportedUnits): array
    {
        $fields = EnumMap::for(ChronoUnit::class, 'bool', false);

        foreach (ChronoField::values() as $field) {
            $fields->put($field, false);
        }

        foreach ($supportedUnits as $field) {
            $fields->put($field, true);
        }

        $data = [];

        foreach ($fields as $field => $supported) {
            /** @var $field ChronoField */
            $data[$field->toString()] = [$field, $supported];
        }

        return $data;
    }
}
