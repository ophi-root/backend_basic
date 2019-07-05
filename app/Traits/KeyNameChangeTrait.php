<?php
namespace App\Traits;

use Illuminate\Support\Collection;

trait KeyNameChangeTrait
{
    public function camelToSnake($data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[snake_case($key)] = $value;
        }
        return $result;
    }
}