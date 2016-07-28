<?php
namespace Peanut\Console;

class Command
{
    /**
     * @var string
     */
    public $command = '';

    /**
     * @var string
     */
    public $path = '';

    /**
     * @return mixed
     */
    public function getCommand()
    {
        if ($this->command) {
            return $this->command;
        } else {
            throw new \Exception('not found command variable');
        }
    }

    /**
     * @param \Peanut\Console\Application $app
     */
    public function execute(\Peanut\Console\Application $app)
    {
        return new Helper($app);
    }

    /**
     * @param \Peanut\Console\Application $app
     */
    public function configuration(\Peanut\Console\Application $app) {}
    /**
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
}
