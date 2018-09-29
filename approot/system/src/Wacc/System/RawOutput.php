<?php
/**
 * BasicHtmlOutputInterface.php
 * $Id$
 *
 * DESCRIPTION
 *  Buffer class for raw output
 *
 * @link https://nxsys.org/spaces/wacc
 * @package WACC\System
 * @license https://nxsys.org/spaces/wacc/wiki/License
 * Please see the license.txt file or the url above for full copyright and
 * license terms.
 * @copyright Copyright 2013-2015 Nexus Systems, Inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */
namespace Wacc\System;

use \Symfony\Component\Console as sfConsole;

/**
 * RawOutput (Buffered)
 * @todo ...a lot...
 */
class RawOutput extends sfConsole\Output\Output
{
	public $sBuffer;

    /**
     * Writes a message to the html buffer.
     *
     * @param string  $message A message to write to the output
     * @param Boolean $newline Whether to add a "br\n" or not
     */
	function doWrite($message, $newline)
	{
		if($newline)
		{
			//@hack will prob break stuff
			if(substr($message,0,5)=="<?xml")
			{
				$message = nl2br($message, true);
			}
		}

		if($newline)
		{
			$message .= "\n";
		}

		$this->sBuffer .= $message;
	}

	public function getOutput()
	{
		return $this->sBuffer;
	}
}
