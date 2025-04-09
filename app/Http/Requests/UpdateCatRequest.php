<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enum\Gender;
use App\Models\Cat;
use Illuminate\Validation\Validator;

class UpdateCatRequest extends CreateCatRequest
{
    protected function after() : array
    {
        return [
            function (Validator $validator) {
                $catId = (int) $this->route()->parameter('id');
                if ($this->get('mother')) {
                    $mother = Cat::query()->find($this->get('mother'));
                    if (! $mother || $mother->gender !== Gender::FEMALE) {
                        $message = 'Mother entity dont exists or gender is not `female`';
                        $validator->errors()->add('mother', $message);
                    } elseif ($mother?->id === $catId) {
                        $message = 'Mother ID can not be equal cat ID';
                        $validator->errors()->add('mother', $message);
                    }
                }
            },
            function (Validator $validator) {
                $catId = (int) $this->route()->parameter('id');
                foreach ($this->get('fathers') ?? [] as $fatherId) {
                    $father = Cat::query()->find($fatherId);
                    if (! $father || $father->gender !== Gender::MALE) {
                        $message = "Father entity with ID ({$fatherId}) dont exists or gender is not `female`";
                        $validator->errors()->add('fathers', $message);
                    }
                    if ($fatherId === $catId) {
                        $message = 'Father ID can not be equal cat ID';
                        $validator->errors()->add('fathers', $message);
                    }
                }
            },
        ];
    }
}
