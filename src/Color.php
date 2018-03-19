<?php
namespace Peanut\Console;

class Color
{
    /**
     * @var array
     */
    public static $backgroundColors = [
        'black'      => '40',
        'red'        => '41',
        'green'      => '42',
        'yellow'     => '43',
        'blue'       => '44',
        'magenta'    => '45',
        'cyan'       => '46',
        'light_gray' => '47',
    ];

    /**
     * @var array
     */
    public static $foregroundColors = [
        'default'    => '39',
        'black'      => '30',
        'red'        => '31',
        'green'      => '32',
        'yellow'     => '33',
        'blue'       => '34',
        'magenta'    => '35',
        'cyan'       => '36',
        'light_gray' => '37',
        'dark_gray'  => '1;30',
        'white'      => '97',
    ];

    /**
     * @var array
     */
    public static $options = [
        'reset'     => '0',
        'bold'      => '1',
        'dark'      => '2',
        'italic'    => '3',
        'underline' => '4',
        'blink'     => '5',
        'reverse'   => '7',
        'concealed' => '8',
    ];

    /**
     * @param  $foregroundColor
     * @param  $args
     * @return mixed
     */
    public static function __callStatic($foregroundColor, $args)
    {
        $string        = $args[0];
        $coloredString = '';

        if (false === isset(self::$foregroundColors[$foregroundColor])) {
            exit($foregroundColor.' not a valid color');
        }

        $coloredString .= self::build(self::$foregroundColors[$foregroundColor]);

        array_shift($args);

        foreach ($args as $option) {
            if (true === isset(self::$backgroundColors[$option])) {
                $coloredString .= self::build(self::$backgroundColors[$option]);
            } elseif (true === isset(self::$options[$option])) {
                $coloredString .= self::build(self::$options[$option]);
            }
        }

        $coloredString .= $string;
        $coloredString .= self::build(self::$options['reset']);

        return $coloredString;
    }

    /**
     * @param $str
     * @param $color
     * @param $background_color
     * @param int                 $width
     */
    public static function text($str = '', $color = 'normal', $background_color = '', $width = 0)
    {
        if ($width > 0) {
            $len = mb_strlen($str);

            if ($width > $len) {
                $str = $str.str_repeat(' ', $width - $len);
            }
        }

        echo self::$color($str, $background_color);
    }

    public static function gettext($str = '', $color = 'normal', $background_color = '', $width = 0)
    {
        if ($width > 0) {
            $len = mb_strlen($str);

            if ($width > $len) {
                $str = $str.str_repeat(' ', $width - $len);
            }
        }

        return self::$color($str, $background_color);
    }

    /**
     * @param $style
     */
    public static function build($style)
    {
        return sprintf("\033[%sm", $style);
    }

    /**
     * @param $string
     */
    public static function xfmt($string)
    {
        $arr = explode(PHP_EOL, $string);

        $max = max(array_map('strlen', $arr));

        $s = '';
        $s .= '   '.str_repeat(' ', $max).'   '.PHP_EOL;

        foreach ($arr as $key => $value) {
            $len = strlen($value);
            $val = $value.str_repeat(' ', $max - $len);
            $s .= '   '.$val.'   '.PHP_EOL;
        }

        $s .= '   '.str_repeat(' ', $max).'   ';

        foreach (explode(PHP_EOL, $s) as $content) {
            echo self::white($content, 'red').PHP_EOL;
        }
    }

    /**
     * @param $input
     * @param mixed $matches
     */
    public static function parseTagsRecursive($matches)
    {
        if (is_array($matches)) {
            if ('bg' == $matches['type']) {
                $string = self::build(self::$backgroundColors[$matches['color']]);
            } else {
                $string = self::build(self::$foregroundColors[$matches['color']]);
            }

            $string .= $matches['content']; //.self::build(self::$options['reset']);
        } else {
            $string = $matches.self::build(self::$options['reset']);
        }

        $regex = '#\<(?P<type>bg|color)=(?P<color>[a-z]+)>(?P<content>(?:[^<]|\<(?!/?\\1>)|(?R))+)\</\\1>#U';

        return preg_replace_callback($regex, 'self::parseTagsRecursive', $string);
    }

    /**
     * @param $string
     */
    public static function fmt($string)
    {
        $arr = explode(PHP_EOL, $string);

        $max = max(array_map('strlen', $arr));

        $s = '';
        $s .= '   '.str_repeat(' ', $max).'   '.PHP_EOL;

        foreach ($arr as $key => $value) {
            $len = strlen($value);
            $val = $value.str_repeat(' ', $max - $len);
            $s .= '   '.$val.'   '.PHP_EOL;
        }

        $s .= '   '.str_repeat(' ', $max).'   ';

        $string = self::parseTagsRecursive($s);

        foreach (explode(PHP_EOL, $string) as $content) {
            echo $content.self::build(self::$options['reset']).PHP_EOL;
        }
    }
}
