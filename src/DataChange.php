<?php
namespace DBSellerTask;
use \Exception;

class DataChange
{
    private $mask_full = "/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}|[ 0-9]{1,3}[:]{1}[0-9]{1,2}/"; # Mask

    private $mask_date = "/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/";
    private $mask_hour = "/[0-9]{1,3}[:]{1}[0-9]{1,2}/";
    private $mask_value ="/[0-9]{1,}/";

    private $m30 = [4,6,9,11];
    private $m31 = [1,3,5,7,8,10,12];

    private $default_hour = "00:00";

    private $day = null;
    private $month = null;
    private $year = null;
    private $hour = null;
    private $minute = null;

    private $operator = null;
    private $value = 0;

    /**
     * DataChange constructor.
     * @param $date
     * @param $operator
     * @param $value
     * @throws Exception
     */
    public function __construct($date, $operator, $value)
    {
        if(!$this->parseDate($date)){
            throw new Exception("A data não obedece o formato (d/m/Y)");
        }
        if(!in_array($operator, ["+", "-"])){
            throw new Exception("A operação não é válida. Utilize apenas a operação de soma e subtração (+, -)");
        }
        $this->operator = $operator;
        if(!$this->parseValue($value)){
            throw new Exception("O valor digitado não é válido. Utilize apenas números");
        }
    }

    /**
     * @param $date
     * @return false|int
     */
    private function parseDate($date)
    {
        $test = [];
        $result = preg_match($this->mask_full, $date, $test);
        if(!empty($test)){
            $this->dealDate($date);
        }
        return $result;
    }

    /**
     * @param $value
     * @return false|int
     */
    private function parseValue($value){
        $test = [];
        $result = preg_match($this->mask_value, $value, $test);
        if(!empty($test)){
            if($this->operator == "+"){
                $this->value = abs(intval($test[0]));
            } else {
                $this->value = abs(intval($test[0])) * -1;
            }
        }
        return $result;
    }

    /**
     * @param $date
     */
    private function dealDate($date){
        $arrDate = [];
        $arrHour = [];
        preg_match($this->mask_date, $date, $arrDate);
        preg_match($this->mask_hour, $date, $arrHour);
        $this->fillDate($arrDate[0]);
        if(!empty($arrHour)){
            $this->fillHour($arrHour[0]);
        } else {
            $this->fillHour($this->default_hour);
        }
    }

    /**
     * @param $date
     */
    private function fillDate($date){
        $explode = explode("/", $date);
        $this->day = intval($explode[0]);
        $this->month = intval($explode[1]);
        $this->year = intval($explode[2]);
    }

    /**
     * @param $hour
     */
    private function fillHour($hour){
        $explode = explode(":", $hour);
        $this->hour = intval($explode[0]);
        $this->minute = intval($explode[1]);
    }

    /**
     *
     */
    public function process(){
        $months = $this->value % 60*24;
        $days = intdiv($this->value, 60*24);
        $years = intdiv($this->value, 60*24*365);
        $hours = intdiv($this->value, 60);
        $minutes = $this->value % 60;

        $this->setNewYear($years);
        $this->setNewMonth($months);
        $this->setNewDay($days);
        $this->setNewHour($hours);
        $this->setNewMinute($minutes);

        return $this->formatOutputDate();
    }

    private function setNewYear($newYear){
        $this->year += $newYear;
    }

    private function setNewDay($newDay){
        $sum = $this->day + $newDay;
        $daysMonth = $this->getDaysOnMonth();
        if($this->operator == "+"){
            if($sum > $daysMonth){
                $this->day = $newDay - $daysMonth;
            } else {
                $this->day = $sum;
            }
        } else {
            if($sum > 0){

            }
            $this->day = $daysMonth - $sum;

        }
    }

    /**
     * @param $newMonth
     */
    private function setNewMonth($newMonth){
        if($this->day <= 0){
            if(in_array($this->month, $this->m31)){
                $this->day = 31;
            } elseif (in_array($this->month, $this->m30)){
                $this->day = 30;
            } else {
                $this->day= 28;
            }
        }
        $sum = $newMonth + $this->month;
        if($sum > 12){
            $this->month = $sum - $newMonth;
        } else {
            $this->month = $sum;
        }
    }

    /**
     * @param null $month
     * @return int
     */
    private function getDaysOnMonth($month = null){
        if(is_null($month)){
            $month = $this->month;
        }
        if(in_array($month, $this->m31)){
            return 31;
        } elseif (in_array($month, $this->m30)){
            return 30;
        } else {
            return 28;
        }
    }

    /**
     * @param $initial
     * @param $final
     */
    private function getDaysOnIntervalOfMonths(){

    }

    /**
     * @param $newHour
     */
    private function setNewHour($newHour){
        if($newHour >= 0){
            $this->hour = ($newHour) % 24;
        } else {
            $this->hour = 23 + ($newHour % 24);
        }
    }

    private function setNewMinute($newMinute){
        if($newMinute >= 0){
            $this->minute = $newMinute % 60;
        } else {
            $this->minute = 60 + ($newMinute % 60);
            $this->hour--;
        }
    }

    /**
     *
     */
    private function formatOutputDate(){
        return
            $this->padding($this->day) . "/" .
            $this->padding($this->month) . "/" .
            $this->padding($this->year) . " ".
            $this->padding($this->hour) . ":" .
            $this->padding($this->minute);
    }

    private function padding($number){
        return str_pad($number, 2, '0', STR_PAD_LEFT);
    }
}