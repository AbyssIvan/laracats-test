<?php

namespace App\Enum;

enum Gender : string
{
    case MALE   = 'male';
    case FEMALE = 'female';

    public function label(): string
    {
        return match ($this) {
            self::MALE   => 'кот',
            self::FEMALE => 'кошка',
        };
    }
}
