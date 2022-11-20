<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\RegisterResource;

class VoucherController extends Controller
{

    private $betBuilder;

    public function __construct(BetBuilder $betBuilder)
    {
        $this->betBuilder = $betBuilder;
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return RegisterResource::collection($request->user()
            ->registers()
            ->paginate($request->input('per_page', 10)));
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
