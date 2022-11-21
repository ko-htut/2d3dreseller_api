<?php

namespace App\Http\Controllers\Api;

use App\Builders\BetBuilder;
use App\Http\Controllers\Controller;
use App\Http\Resources\BetResource;
use App\Http\Resources\CurrentRegisterTotalResource;
use App\Http\Resources\RegisterNumberResource;
use App\Http\Resources\RegisterResource;
use App\Models\Number;
use App\Models\Register;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * @var BetBuilder
     */
    private $betBuilder;

    public function __construct(BetBuilder $betBuilder)
    {
        $this->betBuilder = $betBuilder;
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return RegisterResource::collection($request->user()
            ->registers()
            ->where('date', $request->input('date', now()->format('Y-m-d')))
            ->paginate($request->input('per_page', 10)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->user()->registers()->create([
                'date'      => now()->toDateString(),
                'opened_at' => now(),
            ]);
            return response()->json(['message' => 'အရောင်းစာရင်း ဖွင့်တာ အောင်မြင်ပါတယ်'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'စာရင်းဖွင့်တဲ့ အချိန်မှာ တခုခု မှားယွင်းနေပါတယ်'], 500);
        }
    }

    public function show(Request $request, Register $register)
    {
        return new RegisterResource($register);
    }

    public function betsList(Request $request, Register $register)
    {
        return BetResource::collection($register->bets()->paginate($request->input('per_page', 3)));
    }

    /**
     * @param Request $request
     * @return RegisterResource|JsonResponse
     */
    public function current(Request $request)
    {
        $register = $this->getCurrentRegister();
        return $register ? new RegisterResource($register) : response()->json(['data' => null], 200);
    }

    public function close(Request $request, Register $register)
    {
        try {
            $validator = Validator::make($request->all(),[
                'number' => ['required']
            ]);

            if ($validator->fails())
            {
                return response()->json($validator->errors()->messages(), 422);
            }
            $number = Number::where('number', $request->input('number'))->first();
            $register->update([
                'closed_at' => now(),
                'note' => $request->input('note'),
            ]);
            $register->number()->associate($number);
            $register->save();
            return response()->json(['message' => 'အရောင်းစာရင်း ပိတ်တာ အောင်မြင်ပါတယ်'], 201);

        }catch (Exception $e) {
            return response()->json(['message' => 'စာရင်းပိတ်တဲ့ အချိန်မှာ တခုခု မှားယွင်းနေပါတယ်'], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function currentRegisterClose(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(),[
                'number' => ['required']
            ]);

            if ($validator->fails())
            {
                return response()->json($validator->errors()->messages(), 422);
            }

            $number = Number::where('number', $request->input('number'))->first();
            $register = $this->getCurrentRegister();
            $register->update([
                'closed_at' => now(),
                'note' => $request->input('note'),
            ]);
            $register->number()->associate($number);
            $register->save();
            return response()->json(['message' => 'အရောင်းစာရင်း ပိတ်တာ အောင်မြင်ပါတယ်'], 201);
        }catch (Exception $e) {
            return response()->json(['message' => 'စာရင်းပိတ်တဲ့ အချိန်မှာ တခုခု မှားယွင်းနေပါတယ်'], 500);
        }
    }

    /**
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function list()
    {
        try {
            return RegisterNumberResource::collection(Number::where('type', '2D')->get());
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @return CurrentRegisterTotalResource|JsonResponse
     */
    public function total()
    {
        try {
            $data = [
                'total' => current_register_number_total_amount(false, false, false),
                'types' => collect(config('essentials.types'))->map(function ($type) {
                    return [
                        'type' => $type,
                        'total' => current_register_number_total_amount(false, $type['type'], false),
                    ];
                })
            ];
            return new CurrentRegisterTotalResource($data);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function bet(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'numbers' => ['required']
            ]);

            if ($validator->fails())
            {
                return response()->json($validator->errors()->messages(), 422);
            }
            $this->betBuilder->setNumbers($request->input('numbers'))->build();
            return response()->json(['message' => 'အောင်မြင်ပါတယ်'], 201);
        }catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @return mixed
     */
    private function getCurrentRegister()
    {
        return auth()->user()->registers()
            ->whereDate('opened_at', now()->format('Y-m-d'))
            ->whereNull('closed_at')
            ->first();
    }
}
