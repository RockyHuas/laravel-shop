<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->eloquentBuildExtends();
    }

    private function eloquentBuildExtends()
    {
        MorphTo::macro('_select', function ($columns) {
            $columns = is_array($columns) ? $columns : func_get_args();
            $this->macroBuffer[] = ['method' => 'select', 'parameters' => $columns];
            return $this;
        });

        Builder::macro('with_columns', function (string $relation, array $keys, \Closure $callback = null) {
            return $this->with(["{$relation}" => function ($query) use ($keys, $callback, $relation) {
                if (!empty($keys)) {
                    if ($query instanceof MorphTo) {
                        $table_name = false;
                    } else {
                        $table_name = $query->getRelated()->getTable();
                    }
                    if (is_string($table_name)) {
                        for ($i = 0, $l = count($keys); $i < $l; $i++) {
                            (stripos($keys[$i], '.') === false) and ($keys[$i] = "{$table_name}.{$keys[$i]}");
                        }
                        $query->select($keys);
                    } else {
                        $query->_select($keys);
                    }
                }
                if (is_callable($callback)) {
                    $callback($query);
                }
            }]);
        });

        /**
         * $query->withs('table1:col1,col2', 'table2:col1,col2'); table2 belong to table1
         * $query->withs([
         *     'table1:col1,col2',
         *     'table2:col1,col2' => function($query) {},
         *     'table3:col1,col2'
         *    ])
         */
        Builder::macro('withs', function ($relations) {
            $relations = is_array($relations) ? $relations : func_get_args();
            $relation_define = head(array_keys($relations));
            if (is_string($relation_define)) {
                $relation = $relation_define;
                $callback = array_shift($relations);
            } else {
                $relation = array_shift($relations);
                $callback = null;
            }
            list($table, $columns) = stripos($relation, ':') ? explode(':', $relation) : [$relation, ''];
            $columns = empty($columns) ? [] : explode(',', $columns);
            $this->with_columns($table, $columns, function ($query) use ($relations, $callback) {
                if (is_callable($callback)) {
                    $callback($query);
                }
                if (!empty($relations)) {
                    $query->withs($relations);
                }
            });

            return $this;
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
