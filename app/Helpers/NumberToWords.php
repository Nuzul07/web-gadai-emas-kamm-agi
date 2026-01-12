<?php

namespace App\Helpers;

class NumberToWords
{
    protected static $ones = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan"];
    protected static $teens = ["Sepuluh", "Sebelas", "Dua belas", "Tiga Belas", "Empat Belas", "Lima Belas", "Enam Belas", "Tujuh Belas", "Delapan Belas", "Sembilan Belas"];
    protected static $tens = ["", "", "Dua puluh", "Tiga puluh", "Empat puluh", "Lima puluh", "Enam Puluh", "Tujuh Puluh", "Delapan Puluh", "Sembilan Puluh"];
    protected static $thousands = ["", "Ribu", "Juta", "Miliar", "Triliun"];

    public static function convertToWords($number)
    {
        if ($number == 0) {
            return "nol";
        }

        $words = "";
        $curr = "Rupiah";
        $counter = 0;

        while ($number != 0) {
            $part = $number % 1000;

            if ($part != 0) {
                $words = self::convertThreeDigits($part) . " " . self::$thousands[$counter] . " " . $words;
            }

            $number = (int)($number / 1000);
            $counter++;
        }

        return trim($words . " " . $curr);
    }

    protected static function convertThreeDigits($number)
    {
        $hundreds = (int)($number / 100);
        $tens = $number % 100;

        $words = "";

        if ($hundreds > 0) {
            $words .= self::$ones[$hundreds] . " Ratus ";
        }

        if ($tens > 0) {
            if ($tens < 10) {
                $words .= self::$ones[$tens] . " ";
            } elseif ($tens < 20) {
                $words .= self::$teens[$tens - 10] . " ";
            } else {
                $words .= self::$tens[(int)($tens / 10)] . " " . self::$ones[$tens % 10] . " ";
            }
        }

        return trim($words);
    }
}
