<?php

namespace Database\Factories\Helpers;


class FactoryHelper {
    /**
     * This helper function will fetch a random id from the database, from the given model.
     * If there is no record in the DB, this function will create one.
     * @param string $model FQCN of the model class
     * @return int
     */
    public static function getRandomModelId(string $model)
    {
        $count = app()->make($model)->query()->count();
        if ($count === 0) {
            return $model::factory()->create()->id;
        } else{
            return rand(1, $count);
        }
    }
}
