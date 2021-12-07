<?php

namespace App\Services;

use App\Models\Number;
use Illuminate\Support\Str;

class BetNumbers
{
    protected $numbers = [];

    /**
     * @param $numbers
     * @return $this
     */
    public function setData($numbers)
    {
        $rns = Number::where('type', '2D')->pluck('number');
        foreach ($numbers as $number)
        {
            foreach ($number['numbers'] as $i)
            {
                switch ($number['type']) {
                    case 'D':
                        $this->addNumber($i, $number['type'], $number['amount']);
                        break;
                    case 'R':
                        $this->addNumber($i, $number['type'], $number['amount']);
                        $this->addNumber(strrev($i), $number['type'], $number['amount']);
                        break;
                    case 'F':
                        $filtered = $rns->filter(function ($value, $key) use ($i) {
                            return Str::of($value)->startsWith($i);
                        });
                        foreach ($filtered->all() as $f) {
                            $this->addNumber($f, $number['type'], $number['amount']);
                        }
                        break;
                    case 'E':
                        $filtered = $rns->filter(function ($value, $key) use ($i) {
                            return Str::of($value)->endsWith($i);
                        });
                        foreach ($filtered->all() as $f) {
                            $this->addNumber($f, $number['type'], $number['amount']);
                        }
                        break;
                }
            }
        }

        return $this;
    }

    /**
     * @param $number
     * @param $type
     * @param $amount
     */
    private function addNumber($number, $type, $amount)
    {
        $this->numbers[] = [
            'number' => $number,
            'type' => $type,
            'amount' => $amount,
        ];
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->numbers;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return collect($this->numbers)->sum('amount');
    }
}
