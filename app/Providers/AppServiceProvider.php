<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (env('APP_DEBUG', true)) {
            DB::listen(function ($query) {
                var_dump([
                    $query->sql,
                    $query->bindings,
                    $query->time
                ]);
            });
        }

        QueryBuilder::macro('toRawSql', function () {
            $parametrizedRawSql = $this->toSql();
            $sqlBindings = $this->getBindings();

            return array_reduce($sqlBindings, function ($sql, $binding) {
                $pattern = '/\?/';
                $replacement = is_numeric($binding) ? $binding : "'".$binding."'";
                $limit = 1;

                return preg_replace($pattern, $replacement, $sql, $limit);
            }, $parametrizedRawSql);
        });

        EloquentBuilder::macro('toRawSql', function () {
            return $this->getQuery()->toRawSql();
        });
    }
}
