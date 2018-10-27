<?php
/**
 * $BaseName$
 * $Id$
 *
 * DESCRIPTION
 *  Application Entrypoint for ACN
 *
 * @link http://nxsys.org/spaces/aether
 * @link https://onx.zulipchat.com
 *
 * @package Aether
 * @subpackage System
 * @license http://nxsys.org/spaces/aether/wiki/license
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2018 Nexus Systems, inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

/** @namespace Native Namespace */
namespace NxSys\Applications\Aether\ACN;

//Domestic Namespaces
use NxSys\Applications\Aether;
use NxSys\Toolkits\Aether\SDK\Core;

//Framework Namespaces
use Symfony\Component\Console as sfConsole;
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;


class ACNMain extends Core\Boot\Main
{
	public $sShortName='acn';

	public function __contruct(Type $var = null)
	{
		# code...
	}

	public function getShortName(): string
	{
		return $this->sShortName;
	}

	public function getRunMode(): string
	{
		//or maintenance
		return 'default';
	}

	public function maintenanceRun()
	{
		echo "maintenanceRun";
	}

	public function start(): int
	{
		
		$this->log("Started");
		$this->log("//init listeners");
		$this->log("//init handler");		
		$this->log("//start listener threads (acn[2])");
		$a=0;
		do
		{
			$this->log('Heelo wooorrllllddd!!1111');
			$a++;
			# code...
		}
		while ($a <= 10);
		//clean up
		$this->log("//Clean up");
		$this->log("Stopping");
		return 0;
	}


	public function proccessEvent()
	{
		# code...
		echo 'foooozzzzz....';
		throw new \Exception("Error Processing Request", 1);
		
	}
}

