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
use NxSys\Toolkits\Aether\SDK\Core\Boot\Container;

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
		// $hAcnCommsFiber=Container::getDependency('acn.svc.fiber.AcnComms');
		/**
		 * @var Core\Execution\Job\Fiber
		 */
		$hTermCommsFiber=Container::getDependency('acn.svc.fiber.TermComms');
		$oListener = Container::getDependency('acn.svc.TermComms.listener');
		$hTermCommsFiber->setListener($oListener);
		$hTermCommsFiber->start(PTHREADS_INHERIT_CONSTANTS);
		$this->log("//init handler");
		$this->log("//start listener threads (acn[2])");

		// $hTermCommsFiber->start();
		// $hAcnCommsFiber->start();

		$a=0;
		do
		{
			//---housekeeping---
			//are threads up? & healthy

			// $hTermCommsFiber->

			//---message passing---
			//internal?

			//error handling/recovery
			sleep(1);
			$a++;
			# code...
		}
		while ($a <= 120);
		//clean up
		$this->log("//Clean up");
		$this->log("Stopping");
		return 0;
	}


	public function handleEvent(Core\Boot\Event\Event $oEv)
	{
		# code...
		echo 'foooozzzzz....';
		throw new \Exception("Error Processing Request", 1);

	}

	public static function getChannels(): array
	{
		return [];
	}

	public static function getEvents(): array
	{
		return [];
	}

	public static function getPriority(): int
	{
		return -1;
	}
}

