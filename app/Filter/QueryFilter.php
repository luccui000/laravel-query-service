<?php

namespace App\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

abstract class QueryFilter
{
    public const CACHE_PREFIX = '';

    protected string $table = '';
    protected array $tables = [];
    protected array $selects = [];
    protected array $withs = [];
    protected string $orderField = '';

    protected int $limit = 10;
    protected int $page = 1;
    protected Builder $builder;
    protected bool $hasPagination = true;
    protected string $orderDirection = 'DESC';

    /**
     * @throws \Exception
     */
    public function __construct(public Request $request)
    {
        $this->initService();
        $this->initParams();
    }

    /**
     * @throws \Exception
     */
    private function initService(): void
    {
        $config = config('query-service.tables', []);
        $this->setQueryTables($config);

        $this->builder = app(Builder::class);

        $table = $this->request->get('table');
        $this->setTable($table);

        if(!in_array($table, $this->getTables())) {
            throw new \Exception('Table not found');
        }

        $model = new $this->tables[$table];

        $this->builder->setModel($model);
    }

    private function initParams(): void
    {
        //
    }

    public function get()
    {
        $this->apply($this->builder);
        $model = $this->getModel();

        $withSelects = [];
        foreach ($this->getModelColumns($model) as $column) {
            if(str_contains($column, "_id")) {
                $withSelects[] = $column;
            }
        }

        // get with
        $model = $model->when(count($this->withs) > 0, fn($q) => $q->with($this->withs));
        // order data
        $model = $model->orderBy($this->orderField, $this->orderDirection);

        $model = $model->take($this->limit);
        $model = $model->skip($this->getOffset());
        $model = $model->select(array_unique([...$this->selects, ...$withSelects]));

        if($this->hasPagination) {
            // paginate
            return $model->paginate($this->limit);
        } else {
            //get data
            return $model->get();
        }
    }

    public function apply(Builder $builder): void
    {
        $this->setBuilder($builder);

        foreach ($this->getFilters() as $key => $value) {
            if(method_exists($this, $key)) {
                call_user_func_array([$this, $key], array_filter([$value]));
            }
        }
    }

    public function getFilters(): array
    {
        return $this->request->all();
    }

    private function setBuilder(Builder $builder): void
    {
        $this->builder = $builder;
    }

    public function getTables(): array
    {
        return array_keys($this->tables);
    }

    private function setQueryTables(mixed $config): void
    {
        $this->tables = $config;
    }

    public function getModel()
    {
        return $this->builder->getModel();
    }

    /**
     * @param
     * @return array
     * @Description
     * @Author minhluc
     * @Date 3/25/23
     */
    protected function getModelColumns($model): array
    {
        $tableName = $model->getTable();

        return Cache::remember(self::CACHE_PREFIX . $tableName, 100000, function () use ($model, $tableName) {
            return $model->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($tableName);
        });
    }

    /**
     * @param
     * @return float|int
     * @Description
     * @Author minhluc
     * @Date 3/25/23
     */
    protected function getOffset(): float|int
    {
        return ($this->page - 1) * $this->limit;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
