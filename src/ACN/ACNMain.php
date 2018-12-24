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
		$this->log("Starting ACN...");
		$this->log("//init Event Manager");
		$oEventMgr = Container::getDependency('Aether.boot.eventmanager');
		$oEventMgr->addEvent(new Core\Boot\Event\Event("acn.sys", "starting"));


		$this->log("//init listeners");

		// $hAcnCommsFiber=Container::getDependency('acn.svc.fiber.AcnComms');
		/**
		 * @var NxSys\Toolkits\Aether\SDK\Core\Execution\Job\Fiber
		 */
		$hTermCommsFiber=Container::getDependency('acn.svc.fiber.TermComms');
		$oListener = Container::getDependency('acn.svc.TermComms.listener');

		$hTermCommsFiber->setupConstants(Container::getConfigParam('base.constants'));
		$hTermCommsFiber->start(PTHREADS_INHERIT_NONE);
		$hTermCommsFiber->setListener($oListener);

		$this->log("//init handler");
		$this->log("//start listener threads (acn[2])");

		// $hTermCommsFiber->start();
		// $hAcnCommsFiber->start();
		$hTermCommsFiber->setEventQueue($oEventMgr->getQueue());
		$oEventMgr->addHandler($hTermCommsFiber);

		$oEventMgr->addEvent(new Core\Boot\Event\Event("acn.sys", "loopStarted"));
		$a=0;
		do
		{
			//---housekeeping---
			//are threads up? & healthy

			// $hTermCommsFiber->

			//---message passing---
			//internal?

			//error handling/recovery
			$oEventMgr->processEvent();
			sleep(1);
			$a++;
			# code...
		}
		while ($a <= 999999);
		$oEventMgr->addEvent(new Core\Boot\Event\Event("acn.sys", "loopStoped"));
		//clean up
		$this->log("//Clean up");
		$this->log("Stopping");
		$hTermCommsFiber->halt();
		$hTermCommsFiber->join();
		// sleep(5);
		$this->log("ACN exiting...");
		$oEventMgr->addEvent(new Core\Boot\Event\Event("acn.sys", "exited"));
		return 0;
	}


	public function handleEvent(Core\Boot\Event\Event $oEv)
	{
		$this->log("Event: ".$oEv->getEvent(), (array) $oEv);
	}

	public function getChannels(): array
	{
		return [];
	}

	public function getEvents(): array
	{
		return [];
	}

	public function getPriority(): int
	{
		return -1;
	}
}

