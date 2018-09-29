<?php
/**
 * FILENAME
 * $Id$
 *
 * DESCRIPTION
 *  A Core file for Aether.sh
 *
 * @link http://nxsys.org/spaces/aether
 * @link https://onx.zulipchat.com
 *
 * @package Aether
 * @subpackage System
 * @license http://nxsys.org/spaces/aether/wiki/license
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2018 Nexus Systems, inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

/** @namespace Native Namespace */
namespace NxSys\Frameworks\Aether;

/** Local Project Dependencies **/
use NxSys\Frameworks\Aether;

/** Framework Dependencies **/


/** Library Dependencies **/


/**
 * Undocumented class
 *
 * Why does this exist? What does this do?
 *
 * @throws NxSys\Frameworks\Aether\IException Well, does it?
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 */
class AeshCommand  extends sfConsole\Command\Command
{
	//set app
	//connect app
	//sys/debug/config/offline cmds?

    /**
     * Runs the shell.
     */
    public function runShell()
    {
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(true);

        if ($this->hasReadline) {
            readline_read_history($this->history);
            readline_completion_function(array($this, 'autocompleter'));
        }

        $this->output->writeln($this->getHeader());
        $php = null;
        if ($this->processIsolation) {
            $finder = new PhpExecutableFinder();
            $php = $finder->find();
            $this->output->writeln(<<<EOF
<info>Running with process isolation, you should consider this:</info>
  * each command is executed as separate process,
  * commands don't support interactivity, all params must be passed explicitly,
  * commands output is not colorized.
EOF
            );
        }

        while (true) {
            $command = $this->readline();

            if (false === $command) {
                $this->output->writeln("\n");

                break;
            }

            if ($this->hasReadline) {
                readline_add_history($command);
                readline_write_history($this->history);
            }

            if ($this->processIsolation) {
                $pb = new ProcessBuilder();

                $process = $pb
                    ->add($php)
                    ->add($_SERVER['argv'][0])
                    ->add($command)
                    ->inheritEnvironmentVariables(true)
                    ->getProcess()
                ;

                $output = $this->output;
                $process->run(function($type, $data) use ($output) {
                    $output->writeln($data);
                });

                $ret = $process->getExitCode();
            } else {
                $ret = $this->application->run(new StringInput($command), $this->output);
            }

            if (0 !== $ret) {
                $this->output->writeln(sprintf('<error>The command terminated with an error status (%s)</error>', $ret));
            }
        }
    }

    /**
     * Returns the shell header.
     *
     * @return string The header string
     */
    protected function getHeader()
    {
        return <<<EOF

Welcome to the <info>{$this->application->getName()}</info> shell (<comment>{$this->application->getVersion()}</comment>).

At the prompt, type <comment>help</comment> for some help,
or <comment>list</comment> to get a list of available commands.

To exit the shell, type <comment>^D</comment>.

EOF;
    }

    /**
     * Tries to return autocompletion for the current entered text.
     *
     * @param string $text The last segment of the entered text
     *
     * @return Boolean|array A list of guessed strings or true
     */
    private function autocompleter($text)
    {
        $info = readline_info();
        $text = substr($info['line_buffer'], 0, $info['end']);

        if ($info['point'] !== $info['end']) {
            return true;
        }

        // task name?
        if (false === strpos($text, ' ') || !$text) {
            return array_keys($this->application->all());
        }

        // options and arguments?
        try {
            $command = $this->application->find(substr($text, 0, strpos($text, ' ')));
        } catch (\Exception $e) {
            return true;
        }

        $list = array('--help');
        foreach ($command->getDefinition()->getOptions() as $option) {
            $list[] = '--'.$option->getName();
        }

        return $list;
    }

    /**
     * Reads a single line from standard input.
     *
     * @return string The single line from standard input
     */
    private function readline()
    {
        if ($this->hasReadline) {
            $line = readline($this->prompt);
        } else {
            $this->output->write($this->prompt);
            $line = fgets(STDIN, 1024);
            $line = (!$line && strlen($line) == 0) ? false : rtrim($line);
        }

        return $line;
    }

    public function getProcessIsolation()
    {
        return $this->processIsolation;
    }

    public function setProcessIsolation($processIsolation)
    {
        $this->processIsolation = (Boolean) $processIsolation;
    }

}
