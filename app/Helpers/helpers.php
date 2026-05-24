<?php

if (!function_exists('currency_format')) {
    /**
     * Format a number as currency with the ₱ sign, two decimal places, and comma separators.
     *
     * @param float $number
     * @return string
     */
    function currency_format($number) {
        if($number == 0 || $number == null){
            return "-";
        }
        return '₱ ' . number_format((float)$number, 2, '.', ',');
    }
}

if (!function_exists('zero_checker')) {
    /**
     * Format a number as a percentage with two decimal places and a percentage sign.
     *
     * @param float $number
     * @return string
     */
    function zero_checker($number) {
        if ($number == 0 || $number == null) {
            return "-";
        }
        return $number;
    }
}
