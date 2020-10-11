<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Exceptions\IncorrectResourceIdException;
use App\Models\User;

class ReportController extends Controller
{
    /**
     * Display the specified resource.
     * Currently the only available report is: "avg_act".
     * "avg_act" is an average number of posts, created by each user monthly and weekly.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function show(Request $request, string $id): JsonResponse
    {
        return wrapControllerAction(function() use ($request, $id) {
            if ($id !== 'avg_act') {
                $message = 'Currently only "avg_act" report is available, instead "'.$id.'" given.';
                throw new IncorrectResourceIdException($message);
            }

            $searchParameters = [
                'user_id' => $request->query('user_id'),
                'limit' => $request->query('limit'),
                'offset' => $request->query('offset'),
            ];

            $paginatedResponse = (new User)->getAvgActReport($searchParameters);

            return response()->json(
                $paginatedResponse->dataCollection,
                200,
                ['X-Total-Count' => $paginatedResponse->dataCount]
            );
        });
    }
}
