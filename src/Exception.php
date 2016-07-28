<?php
namespace Peanut\Console;

class Exception extends \RuntimeException
{
    /**
     * @param $e
     */
    public function __construct($e)
    {
        if ($e instanceof \Exception) {
            $e = $e->getMessage();
        }

        $e   = '[Peanut\Console\Exception]'.PHP_EOL.$e;
        $arr = explode(PHP_EOL, $e);

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
            echo Color::text($content, 'white', 'red').PHP_EOL;
        }

        //exit();
    }
}
