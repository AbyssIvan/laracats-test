<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCatRequest;
use App\Models\Cat;
use App\Models\CatFather;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: "/api/cats",
    summary: 'Create cat',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(type: CreateCatRequest::class)
    ),
    tags: ['#Cat'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Cat successfully created',
            content: new OA\JsonContent(example: ['message' => 'Cat successfully created'])
        ),
    ]
)]
class CreateCatController extends Controller
{
    public function __invoke(CreateCatRequest $request) : JsonResponse
    {
        $cat = Cat::create($request->requestData());
        foreach ($request->fathers() as $father) {
            $cat->fathers()->save(CatFather::create(['cat' => $cat->id, 'father' => $father]));
        }
        
        return new JsonResponse(
            [
                'message' => 'Cat successfully created'
            ],
            200
        );
    }
}
