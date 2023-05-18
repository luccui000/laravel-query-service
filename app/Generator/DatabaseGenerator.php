<?php

namespace App\Generator;

use App\Factory\Database\DateTimeFactory;
use App\Factory\Database\DoubleFactory;
use App\Factory\Database\IntFactory;
use App\Factory\Database\TextFactory;
use App\Factory\Database\TimestampFactory;
use App\Factory\Database\TinyIntFactory;
use App\Factory\Database\UnsignedIntFactory;
use App\Factory\Database\VarcharFactory;
use App\Factory\General\EmailFactory;
use App\Factory\General\IDFactory;
use App\Factory\General\NameFactory;
use App\Factory\General\PasswordFactory;
use App\Factory\General\TokenFactory;
use App\Reflect\DatabaseReflect;
use Illuminate\Support\Str;

class DatabaseGenerator
{
    protected string $table;
    public function __construct(
        protected DatabaseReflect $databaseReflect
    )
    {
        $this->table = $this->databaseReflect->getTable();
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function makeData(): array
    {
        $data = [];
        $reflectData = $this->databaseReflect->getData();
        foreach ($reflectData as $field => $value) {
            $generated = $this->makeDefault($field, $value['type']);
            $generated = $this->makeIfId($field, $generated);
            $generated = $this->makeIfEmail($field, $generated);
            $generated = $this->makeIfPassword($field, $generated);
            $generated = $this->makeIfName($field, $generated);
            $generated = $this->makeIfToken($field, $generated);
            $data[$field] = $generated;
        }
        return $data;
    }

    public function makeDefault($field, $type): string
    {
        return match ($type) {
            'VARCHAR' => new VarcharFactory($this->table, $field),
            'INT' => new IntFactory(),
            'BIGINT UNSIGNED'=> new UnsignedIntFactory(),
            'TINYINT' => new TinyIntFactory(),
            'DATETIME' => new DateTimeFactory(),
            'TIMESTAMP' => new TimestampFactory(),
            'DOUBLE' => new DoubleFactory(),
//            'TEXT', 'LONGTEXT' => new TextFactory($this->table, $field),
            default => ''
        };
    }

    private function makeIfEmail($field, $generated): string
    {
        if(in_array($field, EmailFactory::fields())) {
            return new EmailFactory();
        }
        return $generated;
    }

    private function makeIfPassword($field, $generated): string
    {
        if(in_array($field, PasswordFactory::fields())) {
            return new PasswordFactory();
        }

        return $generated;
    }

    private function makeIfName($field, $generated): string
    {
        if(in_array($field, NameFactory::fields())) {
            return new NameFactory();
        }

        return $generated;
    }

    private function makeIfToken(string $field, string $generated): string
    {
        if(in_array($field, TokenFactory::fields())) {
            return new TokenFactory();
        }
        return $generated;
    }

    private function makeIfId($fields, string $generated): string
    {
        if($fields == 'id' || Str::endsWith($fields, '_id')) {
            return new IDFactory($this->table, $fields);
        }

        return $generated;
    }
}
