<?php

use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\DB;
use App\Exceptions\IncorrectResourceIdException;
use App\Lib\DB\PaginatedResponse;

if (!function_exists('runPaginatedQuery')) {
    /**
     * Run COUNT query using given Eloquent query builder.
     * !!!Notice, an exceptions handling must be accomplished by the caller.
     *
     * @param EloquentBuilder $builder
     * @param array $queryParameters
     * @return PaginatedResponse
     * @throw \Exception
     */
    function runPaginatedQuery(EloquentBuilder $builder, array $queryParameters): PaginatedResponse
    {
        $defaultPageLimit = 5;
        $defaultPageOffset = 0;
        $defaultOrderFields = ['id'];
        $defaultOrderDirections = ['asc'];

        $limit = isset($queryParameters['limit'])
            ? $queryParameters['limit']
            : env('DB_DEFAULT_QUERY_LIMIT', $defaultPageLimit);

        $offset = isset($queryParameters['offset']) ? $queryParameters['offset'] : $defaultPageOffset;

        $orderFields = isset($queryParameters['orderFields'])
            ? explode(',', $queryParameters['orderFields'])
            : $defaultOrderFields;

        $orderDirections = isset($queryParameters['orderDirections'])
            ? explode(',', $queryParameters['orderDirections'])
            : $defaultOrderDirections;

        $countBuilder = clone $builder;
        $sqlCount = 'SELECT COUNT(1) AS count FROM(%s) AS data;';
        $rawSqlCountQuery = sprintf($sqlCount, $countBuilder->toRawSql());
        $countResult = DB::select($rawSqlCountQuery);

        if (count($orderFields) !== count($orderDirections)) {
            throw new \Exception('Amounts of orderFields and orderDirections must match.');
        }

        foreach ($orderFields as $index => $field) {
            $builder->orderBy($field, $orderDirections[$index]);
        }

        return new PaginatedResponse(
            $builder->limit($limit)->offset($offset)->get(),
            $countResult[0]->count
        );
    }
}

if (!function_exists('wrapControllerAction')) {
    /**
     * Wraps given controller's method with the try-catch block.
     * Handles most frequent exceptions.
     *
     * !!!Notice:
     * 1. Following implementation, as it currently written, isn't intended for production use, because it is simplified.
     * 2. Exception messages are returned as is.
     *
     * @param callable $actionCallback
     * @return JsonResponse
     */
    function wrapControllerAction(callable $actionCallback): JsonResponse
    {
        /**
         * Simplified exceptions handler.
         *
         * @param Exception $exception
         * @param int $statusCode
         * @return JsonResponse
         */
        $simplifiedExceptionHandler = function (\Exception $exception, int $statusCode): JsonResponse
        {
            return response()->json($exception->getMessage(), $statusCode);
        };

        try {
            return $actionCallback();
        } catch (IncorrectResourceIdException $exception) {
            return $simplifiedExceptionHandler($exception, 400);
        } catch (ValidationException $exception) {
            return $simplifiedExceptionHandler($exception, 400);
        } catch (QueryException $exception) {
            return $simplifiedExceptionHandler($exception, 500);
        } catch (\Exception $exception) {
            return $simplifiedExceptionHandler($exception, 500);
        }
    }
}
