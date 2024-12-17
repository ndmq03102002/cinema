<?php

namespace App\Http\Traits;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait FilterTrait
{
    /**
     * @var string[]
     */
    protected $condition = ["RANGE", "IN", "NOTIN"];

    /**
     * Author Khoa.nd
     * @param Builder $builder
     * @param array|null $searchFields
     * @param string|null $search
     * @return Builder
     */
    public function scopeSearch(Builder $builder, ?array $searchFields = [], ?string $search = ''): Builder
    {
        return $builder->when(!empty($searchFields) && $search, function ($query) use ($searchFields, $search) {
            return $query->where(function ($query) use ($searchFields, $search) {
                foreach ($searchFields as $searchable) {
                    $this->applySearchCondition($query, $searchable, $search);
                }
            });
        });

    }

    /**
     * @param Builder $builder
     * @param string|null $filters
     * @return Builder
     */
    public function scopeFilter(Builder $builder, ?string $filters): Builder
    {
        $filters = json_decode($filters, true);

        return $builder->when(!empty($filters), function ($query) use ($filters) {
            return $query->where(function ($query) use ($filters) {
                foreach ($filters as $key => $value) {
                    $this->applyFilterCondition($query, $key, $value);
                }
            });
        });
    }

    /**
     * @param Builder $builder
     * @param array|null $options
     * @param string|null $aliasTable
     * @return Builder
     */
    public function scopeSort(Builder $builder, ?array $options = [], ?string $aliasTable = ''): Builder
    {
        if (!empty($options)) {
            foreach ($options as $sort) {
                $sortDirection = $sort[0] === '-' ? 'DESC' : 'ASC';
                $sortByColumn = preg_replace('/^[+-]/', '', $sort);
                [$alias, $sortByColumn] = preg_match('/\.(?=[A-Za-z])/', $sortByColumn) ? explode('.', $sortByColumn) : [$aliasTable, $sortByColumn];
                $builder->orderBy($aliasTable ? "{$alias}.{$sortByColumn}" : $sortByColumn, $sortDirection);
            }
        }

        return $builder;
    }

    /**
     * @param $query
     * @param $searchable
     * @param $search
     * @return void
     */
    private function applySearchCondition($query, $searchable, $search): void
    {
        if (str_contains($searchable, '.')) {
            list($relation, $column) = $this->getRelationAndColumn($searchable);
            $query->orWhereRelation($relation, $column, 'like', "%$search%");
        } else {
            $query->orWhere($searchable, 'like', "%$search%");
        }
    }

    /**
     * @param $searchable
     * @return array
     */
    private function getRelationAndColumn($searchable): array
    {
        $relation = Str::beforeLast($searchable, '.');
        $column = Str::afterLast($searchable, '.');

        return [$relation, $column];
    }

    /**
     * @param $query
     * @param $key
     * @param $value
     * @return void
     */
    private function applyFilterCondition($query, $key, $value): void
    {
        list($column, $condition) = $this->parseKey($key);
        if (is_array($value)) {
            $this->applyArrayFilterCondition($query, $column, $condition, $value);
        } else {
            $query->where($column, $value);
        }
    }

    /**
     * @param $key
     * @return array
     */
    private function parseKey($key): array
    {
        $keyParts = explode('_', $key);
        $issetCondition = in_array(end($keyParts), $this->condition);
        $condition = $issetCondition ? array_pop($keyParts) : "WHERE";
        $column = implode('_', $keyParts);

        return [$column, $condition];
    }

    /**
     * @param $query
     * @param $column
     * @param $condition
     * @param $value
     * @return void
     */
    private function applyArrayFilterCondition($query, $column, $condition, $value): void
    {
        switch (strtoupper($condition)) {
            case "RANGE":
                $query->whereBetween($column, $value);
                break;
            case "IN":
                $query->whereIn($column, $value);
                break;
            case "NOTIN":
                $query->whereNotIn($column, $value);
                break;
            default:
                break;
        }
    }
}
