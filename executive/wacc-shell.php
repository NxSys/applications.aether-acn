<?php
/**
 * wacc.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Connector for WACC
 *
 * @link https://nxsys.org/spaces/wacc
 * @package WACC\System
 * @license https://nxsys.org/spaces/wacc/wiki/License
 * Please see the license.txt file or the url above for full copyright and
 * license terms.
 * @copyright Copyright 2013-2015 Nexus Systems, Inc.
 *
 * Heavily influenced from \Symfony\Component\Console\Shell; (c) Fabien Potencier <fabien@symfony.com>
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

namespace Symfony\Component\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\PhpExecutableFinder;

chdir(dirname(__FILE__)); //jump from out of the webroot


/**
 * A Shell wraps an Application to add shell capabilities to it.
 *
 * Support for history and completion only works with a PHP compiled
 * with readline support (either --with-readline or --with-libedit)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class Shell
{
    private $application;
    private $history;
    private $output;
    private $hasReadline;
    private $prompt;
    private $processIsolation;

    /**
     * Constructor.
     *
     * If there is no readline support for the current PHP executable
     * a \RuntimeException exception is thrown.
     *
     * @param Application $application An application instance
     */
    public function __construct(Application $application)
    {
        $this->hasReadline = function_exists('readline');
        $this->application = $application;
        $this->history = getenv('HOME').'/.history_'.$application->getName();
        $this->output = new ConsoleOutput();
        $this->prompt = $application->getName().' > ';
        $this->processIsolation = false;
    }

}
