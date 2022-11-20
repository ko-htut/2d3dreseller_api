<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Builders\BetBuilder;
use Auth;
use App\Http\Resources\RegisterResource;
use App\Http\Resources\VoucherCollection;
use App\Models\Voucher;
use App\Models\VoucherItem;

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
        $user = Auth::user();
        $check = Voucher::where('is_close', 0)->where('user_id', $user->id)->get();
        if(count($check) > 0){
            return response()->json([
                'status'    => false,
                'message'   => 'ဖွင့်လက်စ အရောင်းစာရင်းရှိပါသည်'
            ]);
        }

        $data = new Voucher;
        $data->voucher_code = ""; //
        $data->note = $request->note;
        $data->date = now()->toDateString();
        $data->opened_at = now();
        if($data->save()){
            return response()->json([
                'status'    => true,
                'message'   => 'အရောင်းစာရင်း ဖွင့်ခြင်းအောင်မြင်ပါသည်'
            ]);
        }



    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $data = Voucher::findOrFail($id);
        $data->is_close = 1;
        $data->close_at = now();
        if($data->update()){
            return response()->json([
                'status'    => true,
                'message'   => 'အရောင်းစာရင်း ပိတ်ခြင်းအောင်မြင်ပါသည်'
            ]);
        }else{
            return response()->json([
                'status'    => true,
                'message'   => 'အရောင်းစာရင်း ပိတ်ခြင်း မအောင်မြင်ပါ'
            ]);
        }

    }

    public function destroy($id)
    {
        //
    }

    public function current()
    {
        $user = Auth::user();
        $data = Voucher::where('is_close', 0)->where('user_id', $user->id)->first();
        if($data){
            return response()->json([
                'status'    => true,
                'message'   => 'Current Voucher',
                'data'      => $data
            ]);
        }else{
            return response()->json([
                'status'    => false,
                'message'   => 'Voucher Not found',
            ]);
        }
    }

    public function bet(Request $request)
    {

        $decoded = json_decode($request->getContent(), true);

        $voucher = Voucher::where('id', $decoded['voucher_id'])->whre('is_close', 0)->first();
        if(!$voucher){
            return response()->json([
                'status'    => false,
                'message'   => 'အရောင်းစာရင်း မတွေ့ပါ'
            ]);
        }

        $toInsert = [];
        foreach($decoded['bet'] as $row){
            $data = [
                'voucher_id'    => $decoded['voucher_id'],
                'number'        => $row['number'],
                'amount'        => $decoded['amount']
            ];
            array_push($toInsert, $data);
        }
        VoucherItem::insert($toInsert);

        return response()->json([
            'status'    => true,
            'message'   => 'Success'
        ]);

    }
}
