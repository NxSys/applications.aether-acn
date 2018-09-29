<?php
/**
 * session.php
 * $Id$
 *
 * DESCRIPTION
 *  session Cmdlet for WACC
 *
 * @link https://f2dev.com/prjs/wacc
 * @package WACC
 * @subpackage System
 * @copyright Copyright 2013 F2 Developments, Inc.
 * @license http://f2dev.com/prjs/wacc/license.html
 * Please see the license.txt file or the url above for full copyright and license information.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

namespace Wacc\System\Cmdlets;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

use \Wacc\System as WaccSystem;

class sessionCmdlet extends WaccSystem\Cmdlet
{
	public function configure()
	{
        $this
            ->setName('session')
            ->setDescription('Session Cmdlet V'.WaccSystem\WaccApp::APP_VERSION)
            ->setHelp('hellloooo?');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{

	}
}
