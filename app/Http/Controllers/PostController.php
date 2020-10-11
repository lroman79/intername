<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return wrapControllerAction(function() use ($request) {
            $searchParameters = [
                'user_id' => $request->query('user_id'),
                'full-text-search' => $request->query('full-text-search'),
                'limit' => $request->query('limit'),
                'offset' => $request->query('offset'),
            ];

            $paginatedResponse = (new Post)->search($searchParameters);

            return response()->json(
                $paginatedResponse->dataCollection,
                200,
                ['X-Total-Count' => $paginatedResponse->dataCount]
            );
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        return wrapControllerAction(function() use ($request) {
            $this->validate($request, [
                'title' => ['required', 'string', 'alpha', 'max:255'],
                'body' => ['required', 'string', 'max:2000'],
                'user_id' => ['required', 'numeric'],
            ]);

            $post = (new Post)->createPost([
                'title' => $request->post('title'),
                'body' => $request->post('body'),
                'user_id' => $request->post('user_id'),
            ]);

            return response()->json($post, 201);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        return wrapControllerAction(function() use ($request, $id) {
            // !!!Notice, the $id is validated by Laravel automatically.
            $post = (new Post)->searchById($id);
            return response()->json($post, 200);
        });
    }
}
