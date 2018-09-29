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

class sessionCmdlet extends WaccSystem\Cmdlet
{
	public function configure()
	{
        $this
            ->setName('session')
            ->setDescription('Session Cmdlet V'.WaccSystem\WaccApp::APP_VERSION)
            ->setHelp('Session Utility')
			->addOption('name',null,InputOption::VALUE_REQUIRED,'var name')
			->addOption('value',null,InputOption::VALUE_OPTIONAL,'var value')
		;
	}

	public function execute(InputInterface $oIn, OutputInterface $oPut)
	{
		if($oIn->getOption('name'))
		{
			if($oIn->getOption('value'))
			{
				// note: 0 is wierd... dunno why, but... it is
				WaccSystem\SessionManager::set($oIn->getOption('name'),
											   $oIn->getOption('value'));
			}
			$oPut->writeln(sprintf('The value of %s is "%s"',
								   $oIn->getOption('name'),
								   WaccSystem\SessionManager::get($oIn->getOption('name'))
						  ));
		}
		else
		{
			$oPut->writeln(print_r(WaccSystem\SessionManager::all(),true));
		}
		return;
	}
}
