<?php

namespace NxSys\Applications\Aether\ACN\Listeners;

use NxSys\Applications\Aether\SDK\Core;

use Ratchet;

class RatchetWampHandler implements Ratchet\Wamp\WampServerInterface
{
	public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible)
	{
        $topic->broadcast($event);
    }

	public function onCall(Conn $conn, $id, $topic, array $params)
	{
        $conn->callError($id, $topic, 'RPC not supported on this demo');
	}


}

