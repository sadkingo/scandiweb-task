<?php

namespace App\Types;

final class TypeRegistry
{
    private static array $types = [];

    public static function type(string $classname): BaseType
    {
        return self::$types[$classname] ??= new $classname();
    }
}