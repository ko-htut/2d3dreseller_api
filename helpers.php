<?php

use App\Models\BetNumber;
use App\Models\Register;
use App\Services\Pusher;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (! function_exists('include_route_files')) {

    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function include_route_files($folder)
    {
        try {
            $rdi = new recursiveDirectoryIterator($folder);
            $it = new recursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (! $it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if(! function_exists('paginate')) {
    /**
     * @param $items
     * @param int $perPage
     * @param null $page
     * @return LengthAwarePaginator
     */
    function paginate($items, int $perPage = 15, $page = null): LengthAwarePaginator
    {
        $pageName = 'page';
        $page     = $page ?: (Paginator::resolveCurrentPage($pageName) ?: 1);
        $items    = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path'     => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }
}

if (! function_exists('generateRefNumber')) {
    /**
     * @param string $prefix
     * @param null $model
     * @return string
     */
    function generateRefNumber(string $prefix = 'REF', $model = null): string
    {
        $model = new $model;
        $count = $model->whereDate('created_at', now()->format('Y-m-d'))->count();
        $number = Str::padLeft($count + 1 , 2, '0');
        $time = now()->format('Ymd');
        return "{$prefix}{$time}{$number}";
    }
}

if (! function_exists('current_register')) {
    /**
     * @return mixed
     */
    function current_register()
    {
        return auth()->user()->registers()
        ->whereDate('opened_at', now()->format('Y-m-d'))
        ->whereNull('closed_at')
        ->first();
    }
}

if (! function_exists('get_total_sale_amount')) {
    /**
     * @return mixed
     */
    function get_total_sale_amount($register_id = null, $number = false , $type = 'D', $format = false)
    {
        $q = BetNumber::query();

        if($register_id) $q->whereHas('bet', function (Builder $query) use ($register_id) {
            $query->where('register_id', $register_id);
        });

        if ($number) $q->whereHas('number', function (Builder $query) use ($number) {
            $query->where('number', $number);
        });

        if ($type) $q->where('type' , $type);
        if ($format) return number_format($q->sum('amount'));
        return $q->get()->sum('amount');
    }
}

if (! function_exists('current_register_number_total_amount')) {

    /**
     * @return mixed
     */
    function current_register_number_total_amount($number = false , $type = 'D', $format = false)
    {
        return get_total_sale_amount(current_register()->id, $number , $type, $format);
    }
}
