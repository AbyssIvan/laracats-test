<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\Gender;
use App\Http\Controllers\Controller;
use App\Models\Cat;
use App\Models\CatFather;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[OA\Get(
    path: '/api/cats',
    summary: 'Get cats list',
    tags: ['#Cat'],
    parameters: [
        new OA\Parameter(name: 'filter[gender]', description: 'Cat gender', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: Gender::MALE->value),
        new OA\Parameter(name: 'filter[ageFrom]', description: 'Age from', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 2),
        new OA\Parameter(name: 'filter[ageTo]', description: 'Age to', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 5),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Success response',
            content: new OA\JsonContent(type: 'array', items: new OA\Items(properties: [
                new OA\Property(property: 'id', type: 'integer', example: 2),
                new OA\Property(property: 'name', type: 'string', example: 'Мурка'),
                new OA\Property(property: 'age', type: 'integer', example: 3),
                new OA\Property(property: 'gender', type: 'string', example: 'кот'),
                new OA\Property(property: 'mother', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'Маська'),
                ]),
                new OA\Property(property: 'fathers', type: 'array', items: new OA\Items(properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 5),
                    new OA\Property(property: 'name', type: 'string', example: 'Васька'),
                ])),
            ]))
        ),
    ]
)]
class CatListController extends Controller
{
    public function __invoke(Request $request) : array
    {
        $builder = QueryBuilder::for(Cat::class)
            ->select([
                'cats.*',
            ])
            ->with(['mother'])
            ->allowedFilters([
                AllowedFilter::exact('gender', 'cats.gender'),
                AllowedFilter::callback(
                    'ageFrom',
                    static fn (Builder $builder, string $value) => $builder->where('cats.age', '>=', $value)
                ),
                AllowedFilter::callback(
                    'ageTo',
                    static fn (Builder $builder, string $value) => $builder->where('cats.age', '<=', $value)
                ),
            ])
            ->defaultSort('-cats.id');

        $data = [];
        /** @var Cat $cat */
        foreach ($builder->get() as $cat) {
            $data[] = [
                'id'     => $cat->id,
                'name'   => $cat->name,
                'age'    => $cat->age,
                'gender' => $cat->gender->label(),
                'mother' => $cat->mother ?
                    [
                        'id'   => $cat->mother->id,
                        'name' => $cat->mother->name,
                    ]
                    : null,
                
                'fathers' => $cat->fathers()->count() ?
                    array_map(
                        static fn(CatFather $model) : array => [
                            'id'   => $model->father,
                            'name' => Cat::find($model->father)->name,
                        ],
                        $cat->fathers()->get()->all(),
                    ) :
                    null
            ];
        }

        return $data;
    }
}
