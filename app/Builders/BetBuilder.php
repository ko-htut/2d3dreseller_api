<?php

namespace App\Builders;

use App\Models\Bet;
use App\Models\Number;
use App\Services\BetNumbers;

class BetBuilder
{
    private $numbers;

    /**
     * @var BetNumbers
     */
    private $betNumbers;

    /**
     * @param BetNumbers $betNumbers
     */
    public function __construct(BetNumbers $betNumbers)
    {
        $this->betNumbers = $betNumbers;
    }

    /**
     * @param $numbers
     * @return $this
     */
    public function setNumbers($numbers)
    {
        $this->numbers = $this->betNumbers->setData($numbers);
        return $this;
    }

    public function build()
    {
        $bet = current_register()->bets()->create([
            'ref' => generateRefNumber('REF', Bet::class),
            'total' => $this->numbers->getTotal(),
            'other' => [
                'numbers' => request()->input('numbers')
            ],
            'voucher' => request()->input('voucher')
        ]);

        foreach ($this->numbers->get() as $number) {
            $bet->numbers()->attach(Number::where('number', $number['number'])->first()->id, [
                'type' => $number['type'],
                'amount' => $number['amount'],
            ]);
        }
        $bet->save();
        return $bet;
    }
}
