<?php
/**
 * about.php
 * $Id$
 *
 * DESCRIPTION
 *  Test Cmdlet for WACC
 *
 * @package WACC
 * @subpackage System
 * @copyright © 2012-2022 F2 Developments, Inc. All rights reserved.
 * @license http://f2dev.com/prjs/prj/lic
 *
 * @author Chris Feamster <cfeamster@f2developments.com>
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

class aboutCmdlet extends WaccSystem\Cmdlet
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

	}
}
