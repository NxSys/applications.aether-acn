<?php
/**
 * Cmdlet.php
 * $Id$
 *
 * DESCRIPTION
 *  Base class from which WACC cmdlets are derived
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

namespace Wacc\System;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Abstract Class Cmdlet for all WACC Cmdlets
 *
 */
abstract class Cmdlet extends Command
{
	protected $oLastException=null;

	/**
	 * already decleared empty
	 */
	protected function configure(){}

	/**
	 * SetCode is nonsensical in this exec paradigm (and frankly, is stupid)
	 *
	 */
	protected function execute(InputInterface $input, OutputInterface $output){}

	/**
	 * Allows a cmdlet to report a serious but non fatal problem durring execution.
	 * @param \RuntimeException An exception to report.
	 * @return \RuntimeException The exception reported.
	 */
	protected function registerException(\RuntimeException $e)
	{
		return $this->oLastException=$e;
	}

	/**
	 * Returns the last exception reported, if any.
	 *
	 * If no exception has been reported then this is null.
	 *
	 * @return \RuntimeException
	 */
	public function getLastException()
	{
		return $this->oLastException;
	}
}
