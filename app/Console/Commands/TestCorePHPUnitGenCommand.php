<?php

namespace App\Console\Commands;

use App\Http\Controllers\LucQLController;
use App\Http\Requests\LoginRequest;
use App\Services\ReflectionFormRequest;
use App\Services\ReflectionController;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PhpUnitGen\Core\CoreApplication;

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
        $reflectionController = new ReflectionController(app());
        $controller = new \ReflectionClass(LucQLController::class);
        $methods = $reflectionController->getRoute($controller, 'login');
//        $formRequest = new ReflectionFormRequest(LoginRequest::class);

//        dd($formRequest->getValidRule(''[
//            'required',
//            'string',
//            'min:10',
//            'max:100'
//        ]));
    }
}
