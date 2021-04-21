<?php


namespace Acadea\Boilerplate\Utils;

use Illuminate\Support\Str;

class DataType
{
    public static function standardise($dataType)
    {
        $integers = ['bigInteger', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger', 'unsignedBigInteger', 'unsignedInteger', 'unsignedMediumInteger', 'unsignedSmallInteger', 'unsignedTinyInteger'];
        $booleans = ['binary', 'boolean'];
        $strings = ['char', 'string'];
        $dates = ['date', 'dateTime', 'dateTimeTz'];
        $timestamps = [ 'nullableTimestamps', 'time', 'timeTz', 'timestamp', 'timestampTz'];
        $floats = ['decimal', 'double', 'float', 'unsignedDecimal'];
        $paragraphs = ['longText', 'mediumText', 'text'];
        $intArrays = [];

        if (collect($integers)->contains($dataType)) {
            return 'integer';
        }
        if (collect($booleans)->contains($dataType)) {
            return 'boolean';
        }
        if (collect($strings)->contains($dataType)) {
            return 'string';
        }
        if (collect($dates)->contains($dataType)) {
            return 'date';
        }
        if (collect($floats)->contains($dataType)) {
            return 'float';
        }
        if (collect($paragraphs)->contains($dataType)) {
            return 'text';
        }
        if (collect($timestamps)->contains($dataType)) {
            return 'timestamp';
        }
        if (collect($intArrays)->contains($dataType)) {
            return 'intArrays';
        }
//        if ($dataType === 'ipAddress') {
//            return $faker->ipv4;
//        }
//        if ($dataType === 'json') {
//            return json_encode($faker->randomElements());
//        }
//        if ($dataType === 'macAddress') {
//            return $faker->macAddress;
//        }
//        if ($dataType === 'uuid') {
//            return Str::uuid()->toString();
//        }
//        if ($dataType === 'year') {
//            return $faker->year;
//        }
        return $dataType;
    }
}
