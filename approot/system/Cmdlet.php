<?php
/**
 * Cmdlet.php
 * $Id$
 *
 * DESCRIPTION
 *  Base class from which WACC cmdlets are derived
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
