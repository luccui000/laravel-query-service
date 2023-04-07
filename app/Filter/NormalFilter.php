<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class NormalFilter extends QueryFilter
{
    /**
     * @throws \ReflectionException
     */
    public function select($values)
    {
        foreach ($values as $select) {
            if(str_contains($select, '.')) {
                [$relation, $column] = explode('.', $select);
                if($this->modelHasRelation($this->getModel(), $relation)) {
                    $this->setEagerLoading($relation, $column);
                }
            } else if($select == '*') {
                $this->selects = ["*"];
            } else {
                $this->selects[] = $select;
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function setEagerLoading(string $relation, string $column)
    {
        $column = trim($column, ",");

        if($column == "*") {
            $this->withs[] = $relation;
        } else {
            $foreignKey = $this->getModel()->getForeignKey();
            $returnType = $this->getReturnType($this->getModel(), $relation);
            $parameters = $this->getParameters($this->getModel(), $relation);

            if(is_null($returnType)) {
                $relationReturnType = $this->getModel()->{$relation}($parameters);
                $returnTypeName = get_class($relationReturnType);
            } else {
                $returnTypeName = $returnType->getName();
            }

            $columns = array_map(fn($item) => trim($item), explode( ',', $column));

            if($returnTypeName == BelongsTo::class) {
                $this->withs[$relation] = function($query) use ($columns) {
                    $query->select(array_unique(['id', ...Arr::wrap($columns)]));
                };
            } else if($returnTypeName == HasMany::class) {
                if(str_contains($column, 'id')) {
                    $this->withs[] = "$relation:$column,$foreignKey";
                } else {
                    $this->withs[] = "$relation:id,$column,$foreignKey";
                }
            } else if($returnTypeName == BelongsToMany::class) {
                $this->withs[$relation] = function($query) use($columns) {
                    $query->select([...Arr::wrap($columns)]);
                };
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function order_by($value)
    {
        $model = $this->getModel();
        $modelColumns = $this->getModelColumns($model);

        if(!in_array($value, $modelColumns)) {
            throw new \Exception('Invalid order field');
        }

        $this->orderField = $value;
    }

    public function limit($value)
    {
        $this->limit = 10;
    }

    public function direction($value)
    {
        $this->orderDirection = $value === 'ASC' ? 'ASC' : 'DESC';
    }


    public function search($value)
    {
        if(empty($value)) {
            return ;
        }

        $table = $this->getTable();
        $searchable = config('query-service.searchable');
        $searchFields = data_get($searchable, $table, []);

        if(count($searchFields) == 0) {
            return;
        }

        $tableField = array_filter($searchFields, fn($item) => !str_contains($item, '.'));
        $relationshipField = array_diff($searchFields, $tableField);


        $this->builder->where(function($query) use ($value, $tableField) {
            foreach ($tableField as $field) {
                $query->orWhere($field, "LIKE", "%$value%");
            }
        });

        foreach ($relationshipField as $field) {
            [$relation, $column] = explode('.', $field);
            if($this->modelHasRelation($this->getModel(), $relation)) {
                $this->builder->whereHas($relation, function($query) use ($value, $column) {
                    $query->where($column, 'LIKE', "%$value%");
                });
            }
        }
    }

    public function modelHasRelation($model, $relation): bool
    {
        $reflector = new \ReflectionClass($model);

        if(!$reflector->hasMethod($relation)) {
            return false;
        }

        return true;
    }
    /**
     * @throws \ReflectionException
     */
    private function getReturnType($model, $relation): \ReflectionIntersectionType|\ReflectionNamedType|\ReflectionUnionType|null
    {
        $reflector = new \ReflectionClass($model);
        return $reflector->getMethod($relation)->getReturnType();
    }

    /**
     * @throws \ReflectionException
     */
    private function getParameters($model, $relation): array
    {
        $reflector = new \ReflectionClass($model);
        return $reflector->getMethod($relation)->getParameters();
    }
}
