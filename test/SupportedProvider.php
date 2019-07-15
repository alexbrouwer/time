<?php

namespace PARTest\Time;

use PAR\Enum\EnumMap;
use PAR\Time\Chrono\ChronoField;

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
}
