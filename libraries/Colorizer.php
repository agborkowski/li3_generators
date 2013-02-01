<?php
/**
 * li3_generators console output colorizer
 *
 * @author Mateusz Prażmowski http://li3.it
 * @package li3_generators
 * @subpackage Libraries
 */

namespace li3_generators\libraries;

class Colorizer
{

    /**
     * Foreground color start tags replacements
     *
     * @var array
     */
    private static $foreground = array(
        "{:black}" => "\033[0;30m",
        "{:dark_gray}" => "\033[1;30m",
        "{:red}" => "\033[0;31m",
        "{:bold_red}" => "\033[1;31m",
        "{:green}" => "\033[0;32m",
        "{:bold_green}" => "\033[1;32m",
        "{:brown}" => "\033[0;33m",
        "{:yellow}" => "\033[1;33m",
        "{:blue}" => "\033[0;34m",
        "{:bold_blue}" => "\033[1;34m",
        "{:purple}" => "\033[0;35m",
        "{:bold_purple}" => "\033[1;35m",
        "{:cyan}" => "\033[0;36m",
        "{:bold_cyan}" => "\033[1;36m",
        "{:white}" => "\033[1;37m",
        "{:bold_gray}" => "\033[0;37m",
    );

    /**
     * Background color start tags replacements
     *
     * @var array
     */
    private static $background = array(
        "{:bg_black}" => "\033[40m",
        "{:bg_red}" => "\033[41m",
        "{:bg_magenta}" => "\033[45m",
        "{:bg_yellow}" => "\033[43m",
        "{:bg_green}" => "\033[42m",
        "{:bg_blue}" => "\033[44m",
        "{:bg_cyan}" => "\033[46m",
        "{:bg_light_gray}" => "\033[47m",
    );

    /**
     * Make string appear in foreground color
     */
    public static function fg($string)
    {
        $string = str_replace(array_keys(static::$foreground), array_values(static::$foreground), (string) $string);
        $string = str_replace('{:end}', "\033[0m", $string);

        return $string;
    }

    /**
     * Make string appear with background color
     */
    public static function bg($string)
    {
        $string = str_replace(array_keys(static::$background), array_values(static::$background), $string);
        $string = str_replace('{:end}', "\033[0m", $string);

        return $string;
    }
}

?>