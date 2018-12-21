<?php

namespace NxSys\Applications\Aether\ACN\Listeners;


use NxSys\Toolkits\Aether\SDK\Core\Boot\Event;


use Ratchet;
use Ratchet\ConnectionInterface as Conn;
use Thruway;

class ThruwayWampClient extends Thruway\Peer\Client
{
	public $oThreadContext;
	protected $oRouterSession;
	public $aTerminals = [];
	public function subscribeToAll(...$aArgs)
	{
		# code...
	}
	// public function onOpen($session)
	// {
	// 	printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__);
	// 	$session->subscribe('sh.aether.test', [$this, 'wsEventSink']);
	// 	# code...
	// }

	public function onSessionStart($session, $transport)
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__);
		//var_dump($session->getSessionId());
		$session->subscribe('sh.aether.announce', [$this, 'wsOnAnnounce']);
		$this->oRouterSession = $session;
		//$this->oThreadContext->addEvent(new Event\Event('conn', 'open/sessstart'));
	}

	public function notifyTerminal($iTerminalId, $aData)
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__);
		$this->oRouterSession->publish('sh.aether.term.'.(string) $iTerminalId, null, $aData);
	}
	
	public function wsOnAnnounce($args, $argsKw, $details, $publicationId)
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__);
		var_dump($args, $argsKw);
		var_dump(spl_object_hash($this));
		debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$iSessionId = $argsKw->session;
		$this->aTerminals[$iSessionId] = [];
		var_dump($this->aTerminals);
		$this->oRouterSession->subscribe('sh.aether.acn.' . (string) $iSessionId, [$this, 'wsOnACNMessage']);
		$this->oThreadContext->addEvent(new Event\Event('terminal', 'connect', ['SessionId' => $iSessionId]));
	}

	

	public function wsOnACNMessage($args, $argsKw, $details, $publicationId)
	{
		$iSessionId = $argsKw->session;
		$sChannel = 'terminal'; //Hardcoded to terminal channel to prevent spawning of internal events from external context
		$sEvent = $argsKw->event;
		$aData = $argsKw->data;
		printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__);
		$this->oThreadContext->addEvent(new Event\Event($argsKw->channel, $argsKw->event, (array) $argsKw->data));
	}

	public function setThreadContext($oThread)
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
		var_dump(gettype($oThread));
		$this->oThreadContext = $oThread;
	}

}