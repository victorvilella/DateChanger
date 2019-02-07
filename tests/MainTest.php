<?php
use PHPUnit\Framework\TestCase;
use DBSellerTask\DataChange;


class MainTest extends TestCase
{
    public function sumOneDayTest(){
        $obj = new DataChange("2000-01-01", "+", 60*24);
        $this->assertEquals($obj->process(), "2000-01-02 00:00");
    }

    public function subtractOneDayTest(){
        $obj = new DataChange("2000-01-02", "-", 60*24);
        $this->assertEquals($obj->process(), "2000-01-01 00:00");
    }
}