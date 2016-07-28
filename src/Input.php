<?php
namespace Peanut\Console;

class Input
{
    /**
     * @var mixed
     */
    public $argv;
    /**
     * @var array
     */
    public $argumentList = [];
    /**
     * @var string
     */
    public $applicationName = '';

    /**
     * @param array $argumentList
     */
    public function __construct(array $argv = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'];
        }

        $this->applicationName = array_shift($argv);

        $this->argv = $argv;
    }

    /**
     * @return mixed
     */
    public function getArgumentList()
    {
        $argv = [];

        foreach ($this->argv as $v) {
            if ('-' === $v[0] && false !== strpos($v, '=')) {
                $tmp    = explode('=', $v, 2);
                $argv[] = $tmp[0];
                $argv[] = $tmp[1];
            } else {
                $argv[] = $v;
            }
        }

        $this->argumentList = $argv;

        return $this->argumentList;
    }

    /**
     * @return mixed
     */
    public function getApplicationName()
    {
        return $this->applicationName;
    }
}
