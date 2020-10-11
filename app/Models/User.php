<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Lib\DB\PaginatedResponse;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email'];

    /**
     * Get the posts of the user.
     *
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany('App\Models\Post');
    }

    /**
     * Creates a new user.
     *
     * @param array $parameters
     * @return User
     */
    public function createUser(array $parameters): User
    {
        return static::create($parameters);
    }

    /**
     * Returns a user with given id, if exists.
     *
     * @param int $userId
     * @return mixed
     */
    public function searchById(int $userId)
    {
        return static::find($userId);
    }

    /**
     * Returns the "avg_act" report.
     * "avg_act" is an average number of posts users created monthly and weekly.
     *
     * @param array $queryParameters
     * @return PaginatedResponse
     */
    public function getAvgActReport(array $queryParameters): PaginatedResponse
    {
        $groupsNames = [];

        foreach (['month', 'week'] as $group) {
            $groupsNames[$group] = '
                ROUND(
                    IFNULL(
                        (SELECT AVG(posts_count) FROM (
                            SELECT COUNT(id) AS posts_count
                            FROM `posts`
                            WHERE outer_posts.user_id = user_id
                            GROUP BY '.strtoupper($group).'(created_at)
                        ) AS '.$group.'ly_posts),
                        0
                    ),
                2) AS '.$group.'ly_count
            ';
        }

        $query = static::query()
            ->select([
                'users.id AS user_id',
                'name AS user_name',
                DB::raw($groupsNames['month']),
                DB::raw($groupsNames['week']),
            ])
            ->leftJoin('posts AS outer_posts', 'outer_posts.user_id', '=', 'users.id')
            ->groupBy('users.id');

        if (isset($queryParameters['user_id'])) {
            $query->where('id', $queryParameters['user_id']);
        }

        return runPaginatedQuery($query, $queryParameters);
    }
}
