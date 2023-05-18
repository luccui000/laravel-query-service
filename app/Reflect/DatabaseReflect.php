<?php

namespace App\Reflect;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseReflect extends BaseReflect
{
    protected array $data = [];
    protected Model $model;
    private string $table;
    public function __construct(
        string $model,
        $application = null
    ) {
        parent::__construct($application);
        $this->model = $this->application->make($model);
        $this->setTableName($this->model->getTable());
        $this->getStructure();
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setTableName(string $table)
    {
        $this->table = $table;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getStructure()
    {
        $structures = DB::select("describe {$this->table}");

        return collect($structures)
            ->each(function($item) {
                $length = null;

                if(preg_match('/\d+/i', $item->Type, $m) ) {
                    $length = (int)data_get($m, 0);
                }


                $type = preg_replace('/(\d+)/i', '', $item->Type);
                $type = str_replace("()", "", $type);

                $this->data[$item->Field] = [
                    'field' => $item->Field,
                    'type' => Str::upper($type),
                    'null' => $item->Null,
                    'key' => $item->Key,
                    'default' => $item->Default,
                    'extra' => $item->Extra,
                    'length' => $length
                ];
            });
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }
    public function __get($key)
    {
        if(!$this->has($key)) {
            return;
        }
        return $this->data[$key];
    }

    public function has($key)
    {
        return isset($this->data[$key]);
    }
}
