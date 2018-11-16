<?php

namespace NxSys\Applications\Aether\ACN\Listeners;

use NxSys\Toolkits\Aether\SDK\Core;
use NxSys\Toolkits\Aether\SDK\Core\Boot\Event;
use NxSys\Toolkits\Aether\SDK\Core\Boot\Container;

use NxSys\Applications\Aether\ACN\Listeners\RatchetWampHandler;

use Thruway;
use React;

use Thruway\Transport\RatchetTransport;

class ThruwayCommsWebsocketListener extends Core\Comms\BaseListener
{
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
	
	public function listenLoop(): void
	{
		
		echo "'*'\n\n";
		// debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

		$sWsPath='wsapi';
		$oHandler=new RatchetWampHandler;

		$loop=React\EventLoop\Factory::create();
		var_dump("Making timer");
		// $loop->addPeriodicTimer(1, [$this,'loopMaintenance']);
		$this->oRatchetSockLoop=$loop;
		$server = new Ratchet\App('10.100.0.6', 8354, '0.0.0.0', $loop);
		
		$this->hSockServer=$server;
		#@todo support mounting of multiple routes??
		$server->route('/'.$sWsPath, $oHandler);
		$server->run();
	}

	public function setEventManager(Event\EventManager $oEventManager)
	{
		$this->oEventManager = $oEventManager;
	}

	public function setBinding(string $sHost, string $sInterface, int $iPort)
	{
		# code...
	}

	public function setWsPath(string $sPath)
	{
		# code...
	}

	public function loopMaintenance()
	{
		var_dump("Loop Maintenance");
		$sStatus=$this->oThreadContext->fiberSignal();
		if($sStatus)
		{
			$this->oRatchetSockLoop->end();
			echo "AHHHHHHHHHHHHHHHHHHHHHHHHH";
		}
	}


	public function setThreadContext($oThread)
	{
		var_dump(gettype($oThread));
		$this->oThreadContext = $oThread;
	}
	public function registerLoopHandler(Callable $hHandler){}
}
