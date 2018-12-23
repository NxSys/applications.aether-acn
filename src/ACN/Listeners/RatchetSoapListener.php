<?php

namespace NxSys\Applications\Aether\ACN\Listeners;

use NxSys\Toolkits\Aether\SDK\Core;
use NxSys\Toolkits\Aether\SDK\Core\Boot\Event;
use NxSys\Toolkits\Aether\SDK\Core\Boot\Container;


use Ratchet;
use Thread;

class RatchetSoapListener extends Core\Comms\BaseListener
{
	public function listenLoop(): void
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
		
		
		printf('Loop has returned. Listener is terminating...');
		return;
    }

	public function processEvents()
	{
		while ($this->getThreadContext()->hasIn())
		{
			$oEvent = $this->getThreadContext()->getInEvent();

			if ($oEvent !== null)
			{
				printf("%s saw [%s]%s event.\n", __FUNCTION__, $oEvent->getChannel(), $oEvent->getEvent());
			}
		}
	}
}
