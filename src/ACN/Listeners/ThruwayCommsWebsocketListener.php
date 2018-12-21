<?php

namespace NxSys\Applications\Aether\ACN\Listeners;

use NxSys\Toolkits\Aether\SDK\Core;
use NxSys\Toolkits\Aether\SDK\Core\Boot\Event;
use NxSys\Toolkits\Aether\SDK\Core\Boot\Container;

use NxSys\Applications\Aether\ACN\Listeners\RatchetWampHandler;

use Ratchet;
use React;
use Thread;
use Thruway;

class ThruwayCommsWebsocketListener extends Core\Comms\BaseListener
{
	public $oThreadContext;

	public $hSockServer;
	/** @var string $sHost Hostheader */
	protected $sHost = null;
	/** @var string $sInterface Single IP interface to bind to */
	protected $sInterface = null;
	/** @var int $iPort Port to bind to */
	protected $iPort = null;
	/** @var string $sPath URI part part the WS server will respond to */
	protected $sPath = null;

	/** @var object $oRatchetSockLoop description */
	public $oRatchetSockLoop;

	static $oThruwayHandler;
	
	public function listenLoop(): void
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
		echo "'*'\n\n";
		debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		
		$sWsPath='wsapi';
		//Threaded::extend('Thruway\Peer\Client');



		$oHandler=new ThruwayWampClient("Test");
		$oHandler->setThreadContext($this->getThreadContext());
		$this::$oThruwayHandler = $oHandler;

		// $oHandler->on('open', [$oHandler, 'onSessionStart']);

		$router = new Thruway\Peer\Router;
		$this->oThruwayRouter=$router;
		$transportProvider = new RatchetWampHandler("0.0.0.0", 9090);
		//$oHandler->addTransportProvider($transportProvider);
		
		$router->addTransportProvider($transportProvider);
		$router->addInternalClient($oHandler);
		
		$realmManager = new Thruway\RealmManager();
		$router->registerModule($realmManager);
		$router->setRealmManager($realmManager);
		$router->getLoop()->addPeriodicTimer(.0001, [$this,'eventPump']);
		
		$router->getLoop()->addPeriodicTimer(1, [$this,'loopMaintenance']);
		$realm = new Thruway\Realm("Test");
		$realmManager->addRealm($realm);

		// $oHandler->start(true);
		$router->start(false);
		$router->getLoop()->run();
		//$oHandler->start(false);
		//$oHandler->getLoop()->run();
		printf('Loop has returned. Listener is terminating...');
		return;
	}

	public function setBinding(string $sHost, string $sInterface, int $iPort)
	{
		$this->sHost=$sHost;
		$this->sInterface=$sInterface;
		$this->iPort=$iPort;
	}

	public function setWsPath(string $sPath)
	{
		# code...
	}

	public function eventPump()
	{
		$oEvent = $this->getThreadContext()->getInEvent();

		if ($oEvent !== null)
		{
			printf("%s saw [%s]%s event.\n", __FUNCTION__, $oEvent->getChannel(), $oEvent->getEvent());
			// printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
			//var_dump($oEvent);
			if ($oEvent->getChannel() == "terminal.sys" && $oEvent->getEvent() == "output")
			{
				foreach ($this::$oThruwayHandler->aTerminals as $iTerminalId => $aTermData)
				{
					$this::$oThruwayHandler->notifyTerminal($iTerminalId, ["channel" => "sys", 
														"event" => "output", 
														"session" => (string) $iTerminalId, 
														"data" => ["Output" => $oEvent->Output]]);
				}
			}
		}

	}

	public function loopMaintenance()
	{
		// var_dump("Loop Maintenance");
		$sStatus=$this->getThreadContext()->fiberSignal();
		// printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
		// var_dump($sStatus);
		if($sStatus)
		{
		// printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
			$this->oThruwayRouter->getLoop()->stop();
			echo "AHHHHHHHHHHHHHHHHHHHHHHHHH";
			exit;
			// printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
		}

		//check for ext events
		
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);

		
		//var_dump(spl_object_hash($this));
		//var_dump(spl_object_hash($this::$oThruwayHandler));
		//var_dump($this::$oThruwayHandler->aTerminals);
		var_dump($this::$oThruwayHandler->aTerminals);
		foreach ($this::$oThruwayHandler->aTerminals as $iTerminalId => $aTermData)
		{
			$this::$oThruwayHandler->notifyTerminal($iTerminalId, ["channel" => "sys", 
																"event" => "heartbeat", 
																"session" => (string) $iTerminalId, 
																"data" => ["HEART" => "BEAT"]]);
		}
		
		//send events to thruway

	}

	public function processEvents()
	{
		debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		while ($this->getThreadContext()->hasIn())
		{
			$oEvent = $this->getThreadContext()->getInEvent();

			if ($oEvent !== null)
			{
				printf("%s saw [%s]%s event.\n", __FUNCTION__, $oEvent->getChannel(), $oEvent->getEvent());
				// printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
				//var_dump($oEvent);
				if ($oEvent->getChannel() == "terminal.sys" && $oEvent->getEvent() == "output")
				{
					foreach ($oThruwayHandler->aTerminals as $iTerminalId => $aTermData)
					{
						$oThruwayHandler->notifyTerminal($iTerminalId, ["channel" => "sys", 
															"event" => "output", 
															"session" => (string) $iTerminalId, 
															"data" => ["Output" => $oEvent->Output]]);
					}
				}
			}
		}
	}


	public function setThreadContext($oThread)
	{
		//printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
		//debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

		// var_dump(gettype($oThread));
		$this->oThreadContext = $oThread;
	}

	protected function getThreadContext(): Core\Execution\Job\Fiber
	{
		//printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
		//var_dump(spl_object_hash($this->oThreadContext));
		return $this->oThreadContext;
	}
	public function registerLoopHandler(Callable $hHandler){}
}
