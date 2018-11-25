<?php

namespace NxSys\Applications\Aether\ACN\Handlers;

use NxSys\Toolkits\Aether\SDK\Core\Boot\Event\Event;

class TerminalEndpointHandler
{
	public function handleEvent(Event $oEvent)
	{
		var_dump($oEvent);
		switch ($sEvent->getChannel())
		{
			case '':
				# code...
				break;
			
			default:
				# code...
				break;
		}
	}
}
