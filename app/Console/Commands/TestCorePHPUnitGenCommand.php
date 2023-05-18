<?php

namespace App\Console\Commands;

use App\Http\Controllers\LucQLController;
use App\Reflect\ControllerReflect;
use Illuminate\Console\Command;

class TestCorePHPUnitGenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $reflectionController = new ControllerReflect(app());
        $controller = new \ReflectionClass(LucQLController::class);
        $methods = $reflectionController->getRoute($controller, 'login');
//        $formRequest = new RequestReflect(LoginRequest::class);

//        dd($formRequest->getValidRule(''[
//            'required',
//            'string',
//            'min:10',
//            'max:100'
//        ]));
    }
}
