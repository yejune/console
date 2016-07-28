<?php

namespace Tests;

class ArgvTest extends \PHPUnit_Framework_TestCase
{
    /*
    public function testParse($argv,)
    {

        $app = new \Peanut\Console;

        $app->option('vervose', ['require' => false, 'alias' => 'v|vv|vvv', 'value' => false]);
        $app->option('quiet', ['require' => false, 'alias' => 'q', 'value' => false]);

        $app->command('task', function () use ($app) {
        	$app->all();
        }, 'new Task');

        $app->command('docker', function () use ($app) {
            $app->command('network', function () use ($app) {
                $app->command('ls', 'new Docker\Network\Ls');
                $app->command('rm', 'new Docker\Network\Rm');
                $app->command('create', 'new Docker\Network\Create');
            }, 'new Docker\Network');
            $app->command('run', function () use ($app) {
                $app->argument('command');
            }, 'new Docker\Run');
        }, 'new Docker');

        $app->command('compose', function () use ($app) {
        	$app->option('file', ['require' => false, 'alias' => 'f', 'value' => true]);
        	$app->option('project', ['require' => false, 'alias' => 'p', 'value' => true]);
        	$app->argument('command'); // up/down
            $app->option('detach', ['require' => false, 'alias' => 'd', 'value' => false]);
        }, 'new Compose');
        $app->option('vvv');

        [
            ['console/app', 'compose', '-f', 'compose.yml', '-p', 'default', 'up', '-d'],
            ['console/app', 'compose', '-f=compose.yml', '-p=default', 'up', '-d'],
            ['console/app', 'compose', '--file', 'compose.yml', '--project', 'default', 'up', '--detach'],
            ['console/app', 'compose', '--file=compose.yml', '--project=default', 'up', '--detach'],
        ]
        $a1 = $app->run();

        $_SERVER['argv'] = [
            'test.php',
            'compose',
            '-f',
        	'compose.yml',
        	'-p=default',
            'up',
            '-d'
        ];
        $a2 = $app->run();
        $this->assertEquals($a1, $a2);
    }
    */

    /**
     * @dataProvider additionShortProvider
     */
    public function testShotInput($argv) {
        $expected = ['compose', '-f', 'compose.yml', '-p', 'default', 'up', '-d'];
        $input = new \Peanut\Console\Input($argv);
        $this->assertEquals($expected, $input->getArgumentList());
    }

    public function additionShortProvider() {
        return [
            [['console/app', 'compose', '-f', 'compose.yml', '-p', 'default', 'up', '-d']],
            [['console/app', 'compose', '-f=compose.yml', '-p=default', 'up', '-d']],
            [['console/app', 'compose', '-f=compose.yml', '-p', 'default', 'up', '-d']],
        ];
    }

    /**
     * @dataProvider additionLongProvider
     */
    public function testLongInput($argv) {
        $expected = ['compose', '--file', 'compose.yml', '--project', 'default', 'up', '--detach'];
        $input = new \Peanut\Console\Input($argv);
        $this->assertEquals($expected, $input->getArgumentList());
    }

    public function additionLongProvider() {
        return [
            [['console/app', 'compose', '--file', 'compose.yml', '--project', 'default', 'up', '--detach']],
            [['console/app', 'compose', '--file=compose.yml', '--project=default', 'up', '--detach']],
            [['console/app', 'compose', '--file=compose.yml', '--project', 'default', 'up', '--detach']],
        ];
    }

}
