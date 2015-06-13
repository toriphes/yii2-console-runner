<?php
/**
 * @link https://github.com/toriphes/yii2-console-runner
 * @package toriphes\console
 * @author Giulio Ganci <giulioganci@gmail.com>
 * @copyright Copyright (c) 2015 Giulio Ganci
 * @license BSD-3-Clause
 * @version 1.0
 */

namespace toriphes\console;

use Yii;
use yii\base\Component;

/**
 * Class Runner - a component for running console command in yii2 web applications
 *
 * This extensions is inspired by the project https://github.com/vova07/yii2-console-runner-extension
 *
 * Basic usage:
 * ```php
 * use toriphes\console\Runner;
 * $output = '';
 * $runner = new Runner();
 * $runner->run('controller/action param1 param2 ...', $output);
 * echo $output; //prints the command output
 * ```
 *
 * Application component usage:
 * ```php
 * //you config file
 * 'components' => [
 *     'consoleRunner' => [
 *         'class' => 'toriphes\console\Runner'
 *     ]
 * ]
 * ```
 * ```php
 * //some application file
 * $output = '';
 * Yii::$app->consoleRunner->run('controller/action param1 param2 ...', $output);
 * echo $output; //prints the command output
 * ```
 * @author Giulio Ganci <giulioganci@gmail.com>
 */
class Runner extends Component
{
    /**
     * @var string yii console application file that will be executed
     */
    public $yiiscript;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        set_time_limit(0);

        if($this->yiiscript == null) {
            $this->yiiscript = "@app/yii";
        }
    }

    /**
     * Runs yii console command
     *
     * @param $cmd command with arguments
     * @param string $output filled with the command output
     * @return int termination status of the process that was run
     */
    public function run($cmd, &$output = '')
    {
        var_dump($this->buildCommand($cmd));
        $handler = popen($this->buildCommand($cmd), 'r');

        while(!feof($handler))
            $output .= fgets($handler);

        $output = trim($output);
        $status = pclose($handler);

        return $status;
    }

    /**
     * Builds the command string
     *
     * @param $cmd Yii command
     * @return string full command to execute
     */
    protected function buildCommand($cmd)
    {
        return PHP_BINDIR . '/php ' . Yii::getAlias($this->yiiscript) . ' ' . $cmd . ' 2>&1';
    }
}
