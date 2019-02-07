<?php

class DataChange
{
    private $mask_full = "/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}|[ 0-9]{1,3}[:]{1}[0-9]{1,2}/"; # Mask

    private $mask_date = "/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/";
    private $mask_hour = "/[0-9]{1,3}[:]{1}[0-9]{1,2}/";
    private $mask_value ="/[0-9]{1,}/";

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
        $this->handleYear();
        $this->handleMonth();
        $this->handleDay();
        $this->handleHour();
        $this->handleMinute();
        return "aaa";
    }

    /**
     *
     */
    private function handleMonth(){
        $m30 = [4,6,9,11];
        $m31 = [1,3,5,7,8,10,12];
        $newMonth = $this->month;
        $aux = $this->value;
        if($this->operator == "+"){
            for($i = $this->month; $aux > 0 ; $i++){
                if(in_array($i, $m30)){
                    $aux -= 43200;
                } elseif (in_array($i, $m31)){
                    $aux -= 44640;
                } else{
                    $aux -= 40320;
                }
                $newMonth = $i + 1;
                if($newMonth > 12){
                    $this->year++;
                    $newMonth = 1;
                    $i = 1;
                }
            }
            $this->month = $newMonth;
        } else {
            for($i = $this->month; $aux > 0 ; $i--){
                if(in_array($i, $m30)){
                    $aux -= 43200;
                } elseif (in_array($i, $m31)){
                    $aux -= 44640;
                } else{
                    $aux -= 40320;
                }
                $newMonth = $i - 1;
                if($newMonth < 1){
                    $this->year--;
                    $newMonth = 12;
                    $i = 12;
                }
            }
            $this->month = $newMonth;
        }
    }

    /**
     *
     */
    private function handleMinute(){
        $sum = $this->minute + $this->value;
        if($sum >= 0 && $sum <= 59){
            $this->minute = $sum;
        } else {
            $hour = intdiv($sum, 60);
            $minute = $sum % 60;
            if($hour >= 0){
                $this->hour = $hour;
                $this->minute = $minute;
            } else {
                $this->hour = 24 + $hour;
                $this->minute = 60 + $minute;
            }
        }
    }

    /**
     *
     */
    private function handleHour(){
        $sum = $this->hour + intdiv($this->value , 60);
        if($sum >= 0 && $sum <= 23){
            $this->hour = $sum;
        } else {
            $day = intdiv($sum, 24);
            $hour = $sum % 24;
        }
    }

    /**
     *
     */
    private function handleDay(){

    }

    /**
     *
     */
    private function handleYear(){
        $this->year += intdiv($this->year, 525600);
    }

    /**
     *
     */
    private function formatOutputDate(){

    }
}
if(count($argv) !== 4){
    die("São necessários 3 argumentos (Data, operador, valor)");
}
$date = $argv[1];
$operator = $argv[2];
$value = $argv[3];
try{
    $x = new DataChange($date, $operator, $value);
    echo $x->process();
} catch (Exception $e){
    echo $e->getMessage();
}
