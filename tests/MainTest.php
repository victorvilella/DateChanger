<?php
use PHPUnit\Framework\TestCase;
use DBSellerTask\DataChange;


class MainTest extends TestCase
{
    /**
     * @test
     */
    public function sumOneDayTest(){
        $obj = new DataChange("01/01/2000", "+", 1440);
        $this->assertEquals($obj->process(), "02/01/2000 00:00");
    }

    /**
     * @test
     */
    public function subtractOneDayTest(){
        $obj = new DataChange("02/01/2000", "-", 60*24);
        $this->assertEquals($obj->process(), "01/01/2000 00:00");
    }

    /**
     * @test
     */
    public function addOneMonthTest(){
        $obj = new DataChange("01/01/2000", "+", 60*24*30);
        $this->assertEquals($obj->process(), "31/01/2000 00:00");
    }
}