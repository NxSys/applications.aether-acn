<?php
/**
 * about.php
 * $Id$
 *
 * DESCRIPTION
 *  Test Cmdlet for WACC
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

class mkdirCmdlet extends WaccSystem\Cmdlet
{
	public function configure()
	{
        $this
            ->setName('about')
            ->setDescription('About '.WaccSystem\WaccApp::APP_NAME)
            ->setHelp('hellloooo?');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$sUrl='http://nxsys.org/spaces/wacc';
		$output->writeln(sprintf('<a href="%1$s">%1$s</a>',$sUrl));
	}

	public function uiDisplayAbout()
	{
		$oWA=WaccSystem\WaccApp::getInstance();
		$oWA->oSilex['twig']
			->render('about.twig');
	}
}
