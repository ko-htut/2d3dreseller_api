<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Http\Resources\EssentialResource;
use App\Http\Resources\NumberResource;
use App\Models\Bet;
use App\Models\Number;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Database\Eloquent\Builder;

class EssentialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function numbers(): AnonymousResourceCollection
    {
        return NumberResource::collection(Number::where('type', '2D')->get());
    }

    /**
     * @return EssentialResource
     */
    public function get(): EssentialResource
    {
        $data = [
            'numbers' => Number::where('type', '2D')->get(),
            'types' => config('essentials.types'),
            'dashboard' => [
                'sales' => [
                    'today' => Bet::query()->whereHas('register', function (Builder $query) {
                        $query->where('user_id', auth()->user()->id);
                    })->whereDate('created_at', now())->sum('total'),
                    'week' => Bet::query()->whereHas('register', function (Builder $query) {
                        $query->where('user_id', auth()->user()->id);
                    })->whereBetween('created_at', [
                        now()->subWeek()->format('Y-m-d'), now()->format('Y-m-d')
                    ])->sum('total'),
                    'month' => Bet::query()->whereHas('register', function (Builder $query) {
                        $query->where('user_id', auth()->user()->id);
                    })->whereMonth('created_at', now()->format('m'))->sum('total'),
                ]
            ]
        ];

        return new EssentialResource($data);
    }
}
