<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function test_it_formats_date()
    {
        $date = today()->format('d-m-Y');

        $this->assertEquals(today()->format('Y-m-d'), formatDate($date));
        $this->assertEquals(today()->format('Ymd'), formatDate($date, 'Ymd'));
    }

    public function test_it_formats_date_for_humans()
    {
        $date = now()->subHour()->toIso8601String();

        $this->assertEquals('1 hour ago', diffForHumans($date));
        $this->assertEquals(now()->subDay(2)->isoFormat('MMMM Do, Y'), diffForHumans(now()->subDay(2)->toIso8601String()));
    }
}
