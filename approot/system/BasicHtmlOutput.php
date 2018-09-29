<?php
/**
 * BasicHtmlOutputInterface.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Controler for Web Application Command Console
 *
 * @link https://f2dev.com/prjs/wacc
 * @package WACC
 * @subpackage System
 * @license http://f2dev.com/prjs/wacc/license.html
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2013 F2 Developments, Inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */
namespace Wacc\System;

use \Symfony\Component\Console as sfConsole;

/**
 * BasicHtmlOutput (Buffered)
 * @todo ...a lot...
 */
class BasicHtmlOutput extends sfConsole\Output\Output
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
				$message=nl2br($message,true);
			}
		}

		$msg=sprintf('<span class="output-line">%s</span>',$message);
		if($newline)
		{
			$msg.="\n";
		}
		$this->sBuffer.=$msg;
	}

	public function getOutput()
	{
		return $this->sBuffer;
	}
}
