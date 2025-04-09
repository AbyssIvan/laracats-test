<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cat;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Delete(
    path: "/api/cats/{id}",
    summary: 'Delete cat',
    tags: ['#Cat'],
    parameters: [new OA\Parameter(name: 'id', description: 'Entity id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), examples: ['' => new OA\Examples(example: 'id', summary: 'id', value: 19)])],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Cat successfully deleted',
            content: new OA\JsonContent(example: ['message' => 'Cat successfully deleted'])
        ),
    ]
)]
class DeleteCatController extends Controller
{
    public function __invoke(int $id) : JsonResponse
    {
        $cat = Cat::findOrFail($id);
        $cat->delete();
        
        return new JsonResponse(
            [
                'message' => 'Cat successfully deleted'
            ],
            200
        );
    }
}
