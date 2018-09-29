<?php
/**
 * fancyhello.php
 * $Id$
 *
 * DESCRIPTION
 *  WACC Test Use Cmdlet
 *
 * @category WACC User Cmdlet
 * @link https://f2dev.com/prjs/wacc
 * @package WACC-USER
 * @copyright Copyright 2013 F2 Developments, Inc.
 * @license http://f2dev.com/prjs/wacc/license.html
 * Please see the license.txt file or the url above for full copyright and license information.
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

class fancyhelloCmdlet extends WaccSystem\Cmdlet
{
	public function configure()
	{
        $this
            ->setDefinition(array(
                new InputArgument('name', InputArgument::OPTIONAL, 'The command name', 'help'),
                new InputOption('xml', null, InputOption::VALUE_NONE, 'To output help as XML'),
            ))
            ->setName('fancyhello')
            ->setDescription('Displays a fancy hello (todo)')
            ->setHelp('hellloooo?');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('fancyhello!!! '.$input->getArgument('name'));
	}
}
