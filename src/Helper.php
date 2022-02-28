<?php
namespace Peanut\Console;

class Helper
{
    /**
     * @param  \Peanut\Console\Application $app
     * @return mixed
     */
    public function __construct(\Peanut\Console\Application $app)
    {
        $applicationName = $app->getApplicationName();
        //echo 'error hint'.PHP_EOL;
        $handlers = $app->handlers;

        usort($handlers, function ($a, $b) {
            if ($a->result['cnt'] == $b->result['cnt']) {
                return 0;
            }

            return ($a->result['cnt'] < $b->result['cnt']) ? -1 : 1;
        });
        //print_r($handlers);
        $helper   = $handlers[0];
        $handlers = $app->handlers;
        //print_r($app->handlers);
        echo $helper->result['error'].PHP_EOL.PHP_EOL;
        $tmp = $app->matching['cnt'];
        $s   = $helper->result['cmd'];
        echo 'Usage :'.PHP_EOL;

        foreach ($handlers as $path => $helper) {
            $p  = '#^'.preg_quote($s).'\\\([^\\\]+)'.'$#';
            $p2 = '#^'.preg_quote($s).'$'.'$#';

            if (preg_match($p, $helper->path)
                ||
                preg_match($p2, $helper->path)
            ) {
                $chk = true;
            } else {
                $chk = false;
            }

            //echo PHP_EOL.$p.' '.($helper->path);

            if ($chk) {
                $optIndex = 0;
                $optArr   = [];
                $prt      = [];
                $prt[]    = '   '.$applicationName;

                //print_r($helper->result);
                foreach ($helper->map as $arg) {
                    if (true === is_array($arg)) {
                        $tmp2 = [];

                        //print_r($helper);
                        foreach ($arg as $kkk => $vvv) {
                            $tmp2[] = '-'.$vvv['alias'].', '.$kkk;
                        }

                        $prt[] = '['.implode('|', $tmp2).']';

                        $optIndex++;
                        //$prt[]             = '[OPTIONS'.$optIndex.']';
                        $optArr[$optIndex] = $arg;
                    } elseif ('[' == $arg['0']) {
                        $prt[] = strtoupper($arg);
                    } else {
                        $prt[] = $arg;
                    }
                }

                echo PHP_EOL;
                echo implode(' ', $prt);
            }
        }

        /*
                echo PHP_EOL;
                echo PHP_EOL;

                foreach ($handlers as $path => $helper) {
                    $p = '#^'.preg_quote($s).'\\\([^\\\]+)'.'$#';

        //print_r([$p, $path]);
                    if (preg_match($p, $helper->path)) {
                        $optIndex = 0;
                        $optArr   = [];
                        $prt      = [];
                        $prt[]    = 'Usage';
                        $prt[]    = 'bootapp';
                        foreach ($helper->map as $arg) {
                            if (true === is_array($arg)) {
                                $optIndex++;
                                $prt[]             = '[Options'.$optIndex.']';
                                $optArr[$optIndex] = $arg;
                            } elseif ('[' == $arg['0']) {
                                $prt[] = strtoupper($arg);
                            } else {
                                $prt[] = $arg;
                            }
                        }

                        foreach ($optArr as $key => $arr) {
                            echo 'Options'.$key.' :'.PHP_EOL;
                            foreach ($arr as $k => $v) {
                                echo '   '.str_pad($k, 20, ' ', STR_PAD_RIGHT).'ddddd'.PHP_EOL;
                            }

                            echo PHP_EOL;
                        }
                    }
                }
        */

        echo PHP_EOL;
        //print_r($app);
    }
}
