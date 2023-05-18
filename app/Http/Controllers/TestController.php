<?php

namespace App\Http\Controllers;

use App\Factory\Generator\ClassFactory;
use App\Factory\Generator\MethodFactory;
use App\Factory\Generator\PropertyFactory;
use App\Factory\Generator\TestCaseFactory;
use App\Generator\DatabaseGenerator;
use App\Models\Post;
use App\Models\User;
use App\Reflect\DatabaseReflect;
use App\Structure\TestCaseStructure;

class TestController extends Controller
{
    public function __invoke()
    {
        try {
            $databaseReflect = new DatabaseReflect(User::class);
            $databaseGenerator = new DatabaseGenerator($databaseReflect);
            $data = $databaseGenerator->makeData();

            $testCaseGenerator = new \App\Generator\FeatureTestGenerator(
                new TestCaseStructure(LucQLController::class, 'LucTestController'),
                new ClassFactory(),
                new PropertyFactory(),
                new MethodFactory(),
            );

            $testCaseGenerator->setProviderData($data);
            $testCaseGenerator->setMockup($databaseGenerator);
            $testCaseGenerator->generate(new \ReflectionClass(LucQLController::class));
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
