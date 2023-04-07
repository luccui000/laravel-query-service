<?php

namespace App\Http\Controllers;

use App\Filter\NormalFilter;
use App\Http\Requests\LoginRequest;
use App\Services\LucQLService;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\NoReturn;

class LucQLController extends Controller
{
    public function __construct(Request $request, int $id, string $test, LucQLService $service)
    {
    }

    #[NoReturn] public function __invoke(NormalFilter $filter)
    {
        $response = $filter->get();
        return response()->json($response);
    }

    public function login(LoginRequest $request)
    {

    }

    public function register(Request $request)
    {

    }

    public function logout(Request $request)
    {

    }
}
