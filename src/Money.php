<?php

namespace App;

use InvalidArgumentException;
use KrepyshSpec\World\Currency;

class Money {

    // Scale parameter for bc math functions
    private const SCALE = 14;

    /**
     * The variable $amount must be > 0.
     */

    public function __construct(private string $amount, private string $currency)
    {
        $this->checkValueForPositiveNumber($amount);

        $currency = strtoupper(trim($currency));
        $this->checkCurrencyForExistence($currency);

        $this->currency = $currency;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * The amount in Money object must be positive. After addition, subtraction,
     * multiplication, division, we get the object of money.
     *
     * Therefore, all $values must be positive.
     *
     * Adding, subtracting, multiplying by 0 does not make sense.
     * Division by zero is not possible.
     *
     * $value must be a numeric string for calculation accuracy with bcmath.
     */
    private function checkValueForPositiveNumber(string $value): void
    {
        if(!is_numeric($value) || $value[0] === '0' || $value[0] === '-') {
            throw new InvalidArgumentException('Value: ' . $value . 'is not positive number');
        }
    }

    // The currency must be real, used by people.
    private function checkCurrencyForExistence(string $currency): void
    {
        $allCurrencies = Currency::all();

        if(!in_array($currency, array_keys($allCurrencies), true)) {
            throw new InvalidArgumentException('Invalid currency ' . $currency);
        }
    }

    // When adding or subtracting, the currencies must be the same.
    private function checkCurrencyForEquality(string $otherCurrency): void
    {
        if($this->currency !== $otherCurrency) {
            throw new InvalidArgumentException('Currency ' . $otherCurrency . 'is not equals' . $this->currency);
        }
    }

    public function add(Money $addend): Money
    {
        $this->checkCurrencyForEquality($addend->getCurrency());
        $sum = bcadd($this->amount, $addend->getAmount(), self::SCALE);

        return new Money($sum, $this->currency);
    }

    public function subtract(Money $subtrahend): Money|int
    {
        /**
         * The subtrahend cannot be greater than the minuend, becouse
         * the amount in Money object must be positive.
         *
         * If the subtrahend is equal to minuend return 0.
         */
        if(bccomp($subtrahend->getAmount(), $this->amount, self::SCALE) === 1) {
            throw new InvalidArgumentException('Result can not be negative.');
        }

        $this->checkCurrencyForEquality($subtrahend->getCurrency());
        $result = bcsub($this->amount, $subtrahend->getAmount(), self::SCALE);

        if(bccomp($subtrahend->getAmount(), $this->amount, self::SCALE) === 0) {
            return 0;
        }else{
            return new Money($result, $this->currency);
        }
    }

    /**
     * Multiplier and divisor must be a positive, numeric string.
     *
     * Multiplying and dividing currencies among themselves does not make sense.
     * The exchange rate is a vector.
     *
     * You can multiply and divide a currency by a positive number.
     *
     */
    public function multiply(string $multiplier): Money
    {
        $this->checkValueForPositiveNumber($multiplier);

        $result = bcmul($this->amount, $multiplier, self::SCALE);

        return new Money($result, $this->currency);
    }

    public function divide(string $divisor): Money
    {
        $this->checkValueForPositiveNumber($divisor);

        $result = bcdiv($this->amount, $divisor, self::SCALE);

        return new Money($result, $this->currency);
    }
}
