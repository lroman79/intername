<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Lib\DB\PaginatedResponse;

class Post extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'body', 'user_id'];

    /**
     * Get the posts of the user.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Creates a new post.
     *
     * @param array $parameters
     * @return Post
     */
    public function createPost(array $parameters): Post
    {
        return static::create($parameters);
    }

    /**
     * Returns a post with given id, if exists.
     *
     * @param int $postId
     * @return mixed
     */
    public function searchById(int $postId)
    {
        return static::find($postId);
    }

    /**
     * Returns all the matching posts, that contain a string, specified in the "full-text-search"
     * query-string parameter, in post's body or in post's title.
     * !!!Notice, there is no need to implement searchByUserId(int $userId) and searchByContent(string $content)
     * as separate methods, since it is easy to combine this functionality in one single method -
     * search(array $searchParameters).
     * This approach seems to be more generic.
     *
     * @param array $queryParameters
     * @return PaginatedResponse
     */
    public function search(array $queryParameters): PaginatedResponse
    {
        $query = static::query();

        if (isset($queryParameters['user_id'])) {
            $query->where('user_id', $queryParameters['user_id']);
        }

        if (isset($queryParameters['full-text-search'])) {
            // Searches for given string in post's title and body.
            $searchParameter = '+'.$queryParameters['full-text-search'];
            $sqlMatchAgainst = "MATCH (`title`, `body`) AGAINST (? IN BOOLEAN MODE)";

            $query
                ->select($this->table.'.*')
                ->selectRaw($sqlMatchAgainst.' AS rank', [$searchParameter])
                ->whereRaw($sqlMatchAgainst, [$searchParameter]);

            $queryParameters['orderFields'] = 'rank,id';
            $queryParameters['orderDirections'] = 'desc,asc';
        }

        return runPaginatedQuery($query, $queryParameters);
    }
}
