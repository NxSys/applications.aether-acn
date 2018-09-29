<?php
/**
 * session.php
 * $Id$
 *
 * DESCRIPTION
 *  session Cmdlet for WACC
 *
 * @link https://nxsys.org/spaces/wacc
 * @package WACC\System
 * @copyright Copyright 2013-2015 Nexus Systems, Inc.
 * @license https://nxsys.org/spaces/wacc/wiki/License
 * Please see the license.txt file or the url above for full copyright and
 * license terms.
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

class sessioncmdCmdlet extends WaccSystem\Cmdlet
{
	public function configure()
	{
        $this
            ->setName('sessioncmd')->setAliases(array('sesscmd'))
            ->setDescription('Session Cmdlet V'.WaccSystem\WaccApp::APP_VERSION)
            ->setHelp('Session Utility')
			->addOption('name',InputOption::REQUIRED,'var name')
			->addOption()
		;
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{

	}
}
