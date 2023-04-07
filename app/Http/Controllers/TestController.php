<?php

namespace App\Http\Controllers;

use App\Services\ReflectionController;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function __invoke()
    {
        $reflectionController = new ReflectionController(app());
        $controller = new \ReflectionClass(LucQLController::class);
        $params = $reflectionController->formRequest($controller, 'login');
        dd($params->rules());
        $structure = $reflectionController->getControllerStructure($controller, 'register');
        dd($structure->toArray());
    }
}
