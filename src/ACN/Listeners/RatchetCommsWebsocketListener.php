<?php

namespace NxSys\Applications\Aether\ACN\Listeners;

use NxSys\Applications\Aether\SDK\Core;

use Ratchet;

class RatchetCommsWebsocketListener extends Core\BaseListener
{
	public function listenLoop()
	{
		$sWsPath=Container::getConfigParam('acn.comms.ws.path');
		$oHandler=Container::getConfigParam('acn.comms.ws.handler');
		$oHandler=new RatchetWampHandler;

		$server = new Ratchet\App('localhost');
		$server->route('/'.$sWsPath, $oHandler);
		$server->run();

	}
}
