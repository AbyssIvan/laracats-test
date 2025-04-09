<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCatRequest;
use App\Http\Requests\UpdateCatRequest;
use App\Models\Cat;
use App\Models\CatFather;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Put(
    path: "/api/cats/{id}",
    summary: 'Update cat',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(type: CreateCatRequest::class)
    ),
    tags: ['#Cat'],
    parameters: [new OA\Parameter(name: 'id', description: 'Entity id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), examples: ['' => new OA\Examples(example: 'id', summary: 'id', value: 19)])],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Cat successfully updated',
            content: new OA\JsonContent(example: ['message' => 'Cat successfully updated'])
        ),
    ]
)]
class UpdateCatController extends Controller
{
    public function __invoke(UpdateCatRequest $request, int $id) : JsonResponse
    {
        $cat = Cat::findOrFail($id);
        $cat->update($request->requestData());
        $cat->fathers()->delete();
        foreach ($request->fathers() as $father) {
            $cat->fathers()->save(CatFather::create(['cat' => $cat->id, 'father' => $father]));
        }
        
        return new JsonResponse(
            [
                'message' => 'Cat successfully updated'
            ],
            200
        );
    }
}
