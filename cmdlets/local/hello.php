<?php
/**
 * hello.php
 * $Id$
 *
 * DESCRIPTION
 *  WACC Test Use Cmdlet
 *
 * @category WACC User Cmdlet
 * @link https://nxsys.org/spaces/wacc
 * @package WACC-USER
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

namespace Wacc\User\Cmdlets;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

use \Wacc\System as WaccSystem;

class helloCmdlet extends WaccSystem\Cmdlet
{
	public function configure()
	{
        $this
            ->setDefinition(array(
                new InputArgument('name', InputArgument::OPTIONAL, 'The command name', 'help'),
                new InputOption('xml', null, InputOption::VALUE_NONE, 'To output help as XML'),
            ))
            ->setName('hello')
            ->setDescription('Displays hello ')
            ->setHelp('hellloooo?');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('hello '.$input->getArgument('name'));
	}
}
