<?php
namespace Peanut\Console;

class Result
{
    /**
     * @var string
     */
    private $output;

    /**
     * @param string $output
     */
    public function __construct($output)
    {
        $this->output = $output;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return trim($this->output);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return bool
     */
    public function toBool()
    {
        if ('true' === $this->toString()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if ($this->toString()) {
            return explode("\n", $this->toString());
        } else {
            return [];
        }
    }

    public function jsonToArray()
    {
        return json_decode($this->toString(), true);
    }
}
