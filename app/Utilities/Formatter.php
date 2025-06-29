<?php

namespace App\Utilities;

class Formatter
{
    /**
     * Formata um valor monetário em Reais (BRL)
     * 
     * @param float $value
     * @param bool $showCurrency
     * @return string
     */
    public static function money($value, $showCurrency = false)
    {
        $formatted = number_format($value, 2, ',', '.');
        return $showCurrency ? 'R$ ' . $formatted : $formatted;
    }

    /**
     * Formata um valor monetário em Dólar (USD)
     * 
     * @param float $value
     * @param bool $showCurrency
     * @return string
     */
    public static function moneyUSD($value, $showCurrency = false)
    {
        $formatted = number_format($value, 2, '.', ',');
        return $showCurrency ? 'USD ' . $formatted : $formatted;
    }

    /**
     * Formata uma data no padrão brasileiro
     * 
     * @param string $date
     * @return string
     */
    public static function date($date)
    {
        return date('d/m/Y', strtotime($date));
    }

    /**
     * Formata um número com casas decimais
     * 
     * @param float $number
     * @param int $decimals
     * @return string
     */
    public static function number($number, $decimals = 2)
    {
        return number_format($number, $decimals, ',', '.');
    }

    /**
     * Formata peso (kg) com 4 casas decimais
     * 
     * @param float $weight
     * @return string
     */
    public static function weight($weight)
    {
        return number_format($weight, 4, ',', '.');
    }
}