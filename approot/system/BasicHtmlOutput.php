<?php
/**
 * BasicHtmlOutputInterface.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Controler for Web Application Command Console
 *
 * @package WACC
 * @subpackage System
 * @copyright © 2012-20XX F2 Developments, Inc. All rights reserved.
 * @license http://f2dev.com/prjs/prj/lic
 *
 * @author Chris Feamster <cfeamster@f2developments.com>
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

		$msg=sprintf('<span class="OutputLine">%s</span>',$message);
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
