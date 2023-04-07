<?php

if(!function_exists('getModelTableName')) {
    function getModelTableName($model): mixed {
        if(is_string($model) && str_contains($model, 'App\Models')) {
            $table = new $model();
            return $table->getTable();
        }
        if($model instanceof \Illuminate\Database\Eloquent\Model) {
            return $model->getTable();
        }

        return '';
    }
}
