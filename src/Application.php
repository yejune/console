<?php
namespace Peanut\Console;

class Application
{
    /**
     * @var string
     */
    public $applicationName = '';

    /**
     * @var string
     */
    public $applicationVersion = '';

    /**
     * command 조각
     * @var array
     */
    public $commandParts = [];

    /**
     * command, argument, option 조각
     * @var array
     */
    public $argumentParts = [];

    /**
     * command programe list
     * @var array
     */
    public $handlers = [];

    /**
     * @var array
     */
    public $matchInfomationFormat = [
        'error' => '',
        'cmd'   => '',
        'arg'   => [],
        'opt'   => [],
        'cnt'   => 0
    ];

    /**
     * @var array
     */
    public $optionConfigFormat = [
        'require' => false,
        'alias'   => '',
        'value'   => false
    ];

    /**
     * @var array
     */
    public $matchingHistory = [];

    /**
     * @param $applicationName
     * @param $applicationVersion
     */
    public function __construct($applicationName = '', $applicationVersion = '')
    {
        $this->applicationName    = $applicationName;
        $this->applicationVersion = $applicationVersion;
    }

    /**
     * @param $command
     * @param $callback
     * @param $handler
     */
    public function command($command)
    {
        $this->add($command);
    }

    /**
     * @param $optionKey
     * @param array        $optionConfig
     */
    public function option($optionKey, $optionConfig = [])
    {
        $last = $this->lastCommandPart();

        if (true === is_array($last)) {
            $this->argumentParts[count($this->argumentParts) - 1]['--'.$optionKey] = $optionConfig;
        } else {
            array_push($this->argumentParts, ['--'.$optionKey => $optionConfig]);
        }
    }

    /**
     * @param $arg
     */
    public function argument($arg, $require = true)
    {
        array_push($this->argumentParts, '['.($require ? '' : '?').''.$arg.']');
    }

    public function all()
    {
        array_push($this->argumentParts, '*');
    }

    /**
     * @param $handler
     */
    public function add($command)
    {
        $this->commandHandler = $command;
        $this->push($command->getCommand());
        $command->configuration($this);
        $command->map = $this->argumentParts;

        $path = '\\'.implode('\\', $this->getCommandOnly());
        $command->setPath($path);
        $this->handlers[$path] = $command;

        $this->pop();
    }

    /**
     * @return mixed
     */
    public function getCommandOnly()
    {
        $parts = [];

        foreach ($this->argumentParts as $part) {
            if (true === is_array($part)) {
            } elseif ('[' === $part[0]) {
            } elseif ('*' === $part[0]) {
            } else {
                $parts[] = $part;
            }
        }

        return $parts;
    }

    /**
     * @param $command
     */
    private function push($command)
    {
        array_push($this->argumentParts, $command);
    }

    /**
     * @param $command
     */
    private function pop()
    {
        while (1) {
            $last = $this->lastCommandPart();

            if (false === is_array($last) &&
                true === isset($last[0]) && '[' !== $last[0] && '*' !== $last) {
                // arg, opt가 아닐경우
                break;
            }

            array_pop($this->argumentParts);
        }

        array_pop($this->argumentParts);
    }

    /**
     * @return mixed
     */
    public function getArgumentList()
    {
        $input = new \Peanut\Console\Input();

        return $input->getArgumentList();
    }

    /**
     * @param  $optKey
     * @param  $args
     * @return mixed
     */
    public function getOptionConfig($optKey, $args)
    {
        $optionConfigFormat = $this->optionConfigFormat;

        if (true === isset($args[$optKey])) {
            return $args[$optKey] + $optionConfigFormat;
        }

        foreach ($args as $k => $cnf) {
            if (true === isset($cnf['alias']) && '-'.$cnf['alias'] === $optKey) {
                return $cnf + $optionConfigFormat;
            }
        }
    }

    /**
     * @return mixed
     */
    public function lastCommandPart()
    {
        $count = count($this->argumentParts);

        if (true === isset($this->argumentParts[$count - 1])) {
            return $this->argumentParts[$count - 1];
        } else {
            //return '';
        }
    }

    /**
     * @param $command
     */
    public function getOnlyCommand($command)
    {
        $ret = [];

        foreach ($command as $v) {
            if (true === is_array($v)) {
            } elseif ('[' == $v[0]) {
            } elseif ('*' == $v) {
            } else {
                $ret[] = $v;
            }
        }

        return '\\'.implode('\\', $ret);
    }

    /**
     * @param  $name
     * @return mixed
     */
    public function getArgument($name)
    {
        if (true === isset($this->matching['arg'][$name])) {
            return $this->matching['arg'][$name];
        } else {
            return '';
        }
    }

    /**
     * @param  $name
     * @return mixed
     */
    public function getOption($name)
    {
        if (true === isset($this->matching['opt'][$name])) {
            return $this->matching['opt'][$name];
        } else {
            return '';
        }
    }

    /**
     * @return mixed
     */
    public function getApplicationName()
    {
        return $this->applicationName;
    }

    /**
     * @param  $command
     * @return mixed
     */
    public function check($command)
    {
        $inputList     = $this->getArgumentList();
        $matching      = $this->matchInfomationFormat;
        $matchingCount = 0;

        foreach ($command->map as $index => $commandPart) {
            if (true === is_array($commandPart)) {
                // option

                $inputOption = [];

                while (1) {
                    if (true === is_array($inputList) && 0 < count($inputList)) {
                        $optKey = array_shift($inputList);

                        if (0 === strpos($optKey, '-')) {
                            $config = $this->getOptionConfig($optKey, $commandPart);
                            $optVal = true;

                            if ($config) {
                                if ($config['value']) {
                                    $optVal = array_shift($input);
                                }
                            }

                            $inputOption[$optKey] = $optVal;
                        } else {
                            // 옵션 아니므로 원복
                            array_unshift($inputList, $optKey);
                            break;
                        }
                    } else {
                        break;
                    }
                }

                foreach ($commandPart as $key => $cnf) {
                    if (true === isset($cnf['alias']) && false === isset($inputOption[$key])) {
                        $alias = explode('|', $cnf['alias']);

                        foreach ($alias as $aliasKey => $aliasValue) {
                            if (true === isset($inputOption['-'.$aliasValue])) {
                                $inputOption[$key] = $aliasValue;
                                unset($inputOption['-'.$aliasValue]);
                            }
                        }
                    }

                    if (true === isset($inputOption[$key])) {
                        $matching['opt'][trim($key, '-')] = $inputOption[$key];
                        $matching['cnt']++;
                        unset($inputOption[$key]);
                    } elseif (!$cnf['require']) {
// require check
                    } else {
                        $matching['error'] = 'Option "'.$key.'" not found';
                        break (2);
                    }
                }

                if ($inputOption) {
                    if (true === isset($inputOption['--help'])) {
                        //break(2);
                    }

                    $matching['error'] = 'Option "'.implode(', ', array_keys($inputOption)).'" is not defined.';
                    break;
                }
            } elseif (0 < count($inputList)) {
                $currentInput = array_shift($inputList);

                if ('*' === $commandPart) {
                    // all argument
                    $currentInput .= ' '.implode(' ', $inputList);
                    $matching['arg'][$commandPart] = rtrim($currentInput);
                    $matching['cnt']++;
                    $inputList = [];
                    break;
                } elseif ('[' === $commandPart[0]) {
                    if (1 === preg_match('#^\[\?#', $commandPart) && '-' == $currentInput[0]) {
                        $argKey = ltrim(ltrim(rtrim($commandPart, ']'), '['), '?');

                        $matching['arg'][$argKey] = '';
                        $matching['cnt']++;
                        array_unshift($inputList, $currentInput);

                        continue;
                    } elseif ('-' == $currentInput[0]) {
                        $matching['error'] = 'Command "'.$commandPart.'" is not found, "'.$currentInput.'" found';
                        break;
                    }

                    $argKey = ltrim(ltrim(rtrim($commandPart, ']'), '['), '?');

                    $matching['arg'][$argKey] = $currentInput;
                    $matching['cnt']++;

                    continue;
                } elseif ($currentInput === $commandPart) {
                    // command
                    $matching['cmd'] .= '\\'.$currentInput;
                    $matching['cnt']++;
                    continue;
                }

                $matching['error'] = 'Command "'.$currentInput.'" is error';
                break;
            } else {
                if ('*' === $commandPart) {
                    continue;
                } elseif ('[' === $commandPart[0]) {
                    $jindex  = $index;
                    $require = true;

                    while (1) {
                        if (true === isset($command->map[$jindex])) {
                            if (true === is_array($command->map[$jindex])) {
                                break;
                            }

                            $has = $command->map[$jindex];

                            if (1 === preg_match('#^\[\?#', $has)) {
                                $require = false;
                            } else {
                                $require           = true;
                                $matching['error'] = '"'.str_replace('[?', '[', $commandPart).'" is require';
                                break (2);
                            }
                        } else {
                            break;
                        }

                        $jindex++;
                    }

                    if (false == $require) {
                        // 필수가 아님
                        break;
                    }

                    // argument
                    $matching['error'] = 'Argument "'.str_replace('[?', '[', $commandPart).'" is not found';
                    break;
                }

                $matching['error'] = /*$commandPart.*/'command not found';
                break;
            }
        }

        if ($matching['error']) {
        } elseif ($inputList) {
            // 남음
            $matching['error'] = 'not matching- ['.current($inputList).']';
            $matching['more']  = $inputList;
        }

        return $matching;
    }

    /**
     * @return mixed
     */
    public function match()
    {
        $matched = false;

        foreach ($this->handlers as $path => &$command) {
            $matching = $this->check($command);

            $command->result = $matching;

            $this->matching = $matching;

            if (!$matching['error']) {
                $matched = true;
                break;
            }
        }

        if ($matched) {
            return $this->handlers[$matching['cmd']]->execute($this);
        } else {
            return new \Peanut\Console\Helper($this);
        }
    }

    /**
     * @return mixed
     */
    public function getApplicationVersion()
    {
        return $this->applicationVersion;
    }
}
