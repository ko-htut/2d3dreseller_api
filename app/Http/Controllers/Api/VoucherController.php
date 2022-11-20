<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Builders\BetBuilder;
use App\Http\Resources\RegisterResource;
use App\Http\Resources\VoucherCollection;
use App\Models\Voucher;

class VoucherController extends Controller
{

    private $betBuilder;

    public function __construct(BetBuilder $betBuilder)
    {
        $this->betBuilder = $betBuilder;
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $data = Voucher::orderBy('id', 'desc')->paginate(30);
        return response()->json(new VoucherCollection($data), 200); 
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
