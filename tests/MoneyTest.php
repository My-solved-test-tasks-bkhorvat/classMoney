<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Money;
use InvalidArgumentException;

class MoneyTest extends TestCase
{

  //  Test invalid amount in constructor
  public function testInvalidAmountExceptionIsString(): void
  {
      $this->expectException(InvalidArgumentException::class);
      new Money('asdk', 'USD');
  }

  public function testInvalidAmountExceptionStartWithiZero(): void
  {
      $this->expectException(InvalidArgumentException::class);
      new Money('0123', 'USD');
  }

  public function testInvalidAmountExceptionIsZero(): void
  {
      $this->expectException(InvalidArgumentException::class);
      new Money('0', 'USD');
  }

  public function testInvalidAmountExceptionBinary(): void
  {
      $this->expectException(InvalidArgumentException::class);
      new Money('0b11111111', 'USD');
  }

  public function testInvalidAmountExceptionHexadecimal(): void
  {
      $this->expectException(InvalidArgumentException::class);
      new Money('0x1A', 'USD');
  }

  public function testInvalidAmountExceptionOctal(): void
  {
      $this->expectException(InvalidArgumentException::class);
      new Money('0o123', 'USD');
  }

  public function testInvalidAmountExceptionNegative(): void
  {
      $this->expectException(InvalidArgumentException::class);
      new Money('-123', 'USD');
  }

  //  Test format currency
  public function testConstructorWithInvalidCurrency(): void
  {
      $money = new Money('1.05', 'usd');
      $this->assertEquals('USD', $money->getCurrency());

      $money = new Money('1.05', 'uSd');
      $this->assertEquals('USD', $money->getCurrency());

      $money = new Money('1.05', ' usd   ');
      $this->assertEquals('USD', $money->getCurrency());
  }

  //  Test getters
  public function testMoneyGetters(): void
  {
      $money = new Money('1.05', 'USD');
      $this->assertEquals('1.05', $money->getAmount());
      $this->assertEquals('USD', $money->getCurrency());
  }

  //  Test equalsCurrency method
  public function testEqualsCurrencyWithAdd(): void
  {
      $money1 = new Money('1.05', 'USD');
      $money2 = new Money('2.2', 'EUR');

      $this->expectException(InvalidArgumentException::class);
      $money1->add($money2);
  }

  //  Test CheckCurrencyForExistence method
  public function testCheckCurrencyForExistence(): void
  {
      $this->expectException(InvalidArgumentException::class);
      new Money('3.05', 'islam');
  }

  public function testEqualsCurrencyWithSubtract(): void
  {
      $money1 = new Money('3.05', 'USD');
      $money2 = new Money('2.2', 'EUR');

      $this->expectException(InvalidArgumentException::class);
      $money1->subtract($money2);
  }

  //  Test add
  public function testAdd(): void
  {
      $money1 = new Money('1.05', 'USD');
      $money2 = new Money('2.2', 'USD');
      $money3 = $money1->add($money2);
      $this->assertEquals('3.25000000000000', $money3->getAmount());
      $this->assertEquals('USD', $money3->getCurrency());
  }

  //  Test subtract
  public function testSubtract(): void
  {
      $money1 = new Money('3.05', 'USD');
      $money2 = new Money('2.02', 'USD');
      $money3 = $money1->subtract($money2);
      $this->assertEquals('1.03000000000000', $money3->getAmount());
      $this->assertEquals('USD', $money3->getCurrency());
  }

  public function testSubtrahendIsEqualsMinuend(): void
  {
      $money1 = new Money('3', 'USD');
      $money2 = new Money('3', 'USD');
      $money3 = $money1->subtract($money2);
      $this->assertEquals(0, $money3);
  }

  public function testSubtractIsNegative(): void
  {
      $money1 = new Money('2.02', 'USD');
      $money2 = new Money('3.05', 'USD');

      $this->expectException(InvalidArgumentException::class);
      $money1->subtract($money2);

  }

  //  Test Multiply
  public function testMultiply(): void
  {
      $money1 = new Money('10.02', 'USD');
      $money2 = $money1->multiply('2');

      $this->assertEquals('20.04000000000000', $money2->getAmount());
      $this->assertEquals('USD', $money2->getCurrency());
  }

  //  Test devide
  public function testDivide(): void
  {
      $money1 = new Money('10.02', 'USD');
      $money2 = $money1->divide('2');

      $this->assertEquals('5.01000000000000', $money2->getAmount());
      $this->assertEquals('USD', $money2->getCurrency());
  }

  public function testDivideByZero(): void
  {
      $money1 = new Money('10.02', 'USD');

      $this->expectException(InvalidArgumentException::class);
      $money1->divide('0');
  }
}
