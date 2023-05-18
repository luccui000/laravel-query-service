<?php

namespace App\Http\Controllers;

use App\Filter\NormalFilter;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\LucQLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\NoReturn;

class LucQLController extends Controller
{
    public function __construct()
    {
    }

    #[NoReturn] public function __invoke(NormalFilter $filter)
    {
        $response = $filter->get();
        return response()->json($response);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where([
            'email' => $request->get('email'),
        ])->first();

//        if(!$user) {
//            return response()->json([
//                'success' => false,
//            ], 401);
//        } else {
            return response()->json([
                'success' => true
            ], 200);
//        }

    }

    public function register(Request $request)
    {

    }

    public function logout(Request $request)
    {

    }

    public function create(Request $request)
    {

    }
}
