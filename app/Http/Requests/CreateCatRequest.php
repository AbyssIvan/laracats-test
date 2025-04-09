<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enum\Gender;
use App\Models\Cat;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;
use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
        new OA\Property(property: 'name', description: 'Cat name', type: 'string', example: 'Мурка'),
        new OA\Property(property: 'age', description: 'Age', type: 'integer', example: 3),
        new OA\Property(property: 'gender', description: 'Gender', type: 'string', example: Gender::MALE->value),
        new OA\Property(property: 'mother', description: 'Mother Id', type: 'integer', nullable: true, example: 3),
        new OA\Property(property: 'fathers', description: 'Fathers Id', type: 'array', nullable: true, example: [4, 5], items: new OA\Items()),
    ],
    example: [
        "name"    => 'Мурка',
        "age"     => 3,
        "gender"  => Gender::MALE->value,
        "mother"  => 3,
        "fathers" => [4, 5],
    ]
)]
class CreateCatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @psalm-return array<string, ValidationRule|array|string>
     */
    public function rules() : array
    {
        return [
            'name'      => 'required|string',
            'age'       => 'required|integer',
            'gender'    => ['required', new Enum(Gender::class)],
            'mother'    => 'nullable|integer|min:1',
            'fathers'   => 'nullable|array',
            'fathers.*' => 'integer',
        ];
    }

    protected function after() : array
    {
        return [
            function (Validator $validator) {
                if ($this->get('mother')) {
                    $mother = Cat::query()->find($this->get('mother'));
                    if (! $mother || $mother->gender !== Gender::FEMALE) {
                        $message = 'Mother entity dont exists or gender is not `female`';
                        $validator->errors()->add('mother', $message);
                    }
                }
            },
            function (Validator $validator) {
                foreach ($this->get('fathers') ?? [] as $fatherId) {
                    $father = Cat::query()->find($fatherId);
                    if (! $father || $father->gender !== Gender::MALE) {
                        $message = "Father entity with ID ({$fatherId}) dont exists or gender is not `female`";
                        $validator->errors()->add('fathers', $message);
                    }
                }
            },
        ];
    }

    public function requestData() : array
    {
        return [
            'name'      => $this->get('name'),
            'age'       => $this->get('age'),
            'gender'    => $this->get('gender'),
            'mother_id' => $this->get('mother'),
        ];
    }

    public function fathers() : array
    {
        return $this->get('fathers') ?? [];
    }
}
