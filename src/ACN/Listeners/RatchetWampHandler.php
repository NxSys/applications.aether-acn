<?php

namespace NxSys\Applications\Aether\ACN\Listeners;

use NxSys\Applications\Aether\SDK\Core;

use Ratchet;
use Ratchet\ConnectionInterface as Conn;
use Thruway;
use React\EventLoop\LoopInterface;
use Thruway\Peer\ClientInterface;

class RatchetWampHandler extends Thruway\Transport\RatchetTransportProvider
	implements Ratchet\Wamp\WampServerInterface, Thruway\Transport\ClientTransportProviderInterface
{
	public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible)
	{
		$topic->broadcast($event);
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __METHOD__, __LINE__);
    }
	
	public function onCall(Conn $conn, $id, $topic, array $params)
	{
		$conn->callError($id, $topic, 'RPC not supported on this demo');
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __METHOD__, __LINE__);
	}
	
	public function onSubscribe(Conn $conn, $topic)
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __METHOD__, __LINE__);
		# code...
	}

	public function onUnSubscribe(\Ratchet\ConnectionInterface $conn, $topic)
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __METHOD__, __LINE__);
		# code...
	}

	// public function onOpen(Conn $conn)
	// {
	// 	printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __METHOD__, __LINE__);
	// }

	// public function onClose(\Ratchet\ConnectionInterface $conn)
	// {
	// 	printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __METHOD__, __LINE__);		
	// }
	
	public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e)
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
		parent::onError($conn, $e);
	}

	public function startTransportProvider(ClientInterface $peer, LoopInterface $loop)
	{

	}
}

