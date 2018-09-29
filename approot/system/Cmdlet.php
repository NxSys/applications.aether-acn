<?php
/**
 * Cmdlet.php
 * $Id$
 *
 * DESCRIPTION
 *  Base class from which WACC cmdlets are derived
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

namespace Wacc\System;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

abstract class Cmdlet extends Command
{

	/**
	 * already decleared empty
	 */
	protected function configure(){}

	/**
	 * SetCode is nonsensical in this exec paradigm (and frankly, is stupid)
	 *
	 */
	protected function execute(InputInterface $input, OutputInterface $output){}
}
