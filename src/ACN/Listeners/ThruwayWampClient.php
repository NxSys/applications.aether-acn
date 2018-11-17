<?php

namespace NxSys\Applications\Aether\ACN\Listeners;


use NxSys\Toolkits\Aether\SDK\Core\Boot\Event;


use Ratchet;
use Ratchet\ConnectionInterface as Conn;
use Thruway;

class ThruwayWampClient extends Thruway\Peer\Client
{
	public $oThreadContext;
	public function subscribeToAll(...$aArgs)
	{
		# code...
	}

	public function onSessionStart($session, $transport)
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<\n", __CLASS__, __FUNCTION__, __LINE__);

		$session->subscribe('sh.aether.test', [$this, 'wsEventSink']);
		//$this->oThreadContext->addEvent(new Event\Event('conn', 'open/sessstart'));
	}

	public function wsEventSink($args, $argsKw, $details, $publicationId)
	{
		var_dump($args, $argsKw, $details, $publicationId);
		$value = isset($args[0]) ? $args[0] : '';
        echo 'Received ' . json_encode($value). PHP_EOL;
        $this->oThreadContext->addEvent(new Event\Event($argsKw->channel, $argsKw->event, (array) $argsKw->data));
	}

	public function setThreadContext($oThread)
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
		var_dump(gettype($oThread));
		$this->oThreadContext = $oThread;
	}

}