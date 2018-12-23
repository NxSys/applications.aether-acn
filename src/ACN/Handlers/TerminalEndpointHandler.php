<?php

namespace NxSys\Applications\Aether\ACN\Handlers;

use NxSys\Toolkits\Aether\SDK\Core\Boot\Event\Event;
use NxSys\Toolkits\Aether\SDK\Core\Boot\Container;


/**
 * @Channels terminal|terminal.command|terminal.meta|terminal.sys
 */
class TerminalEndpointHandler
{
	public function handleEvent(Event $oEvent)
	{
		//printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __METHOD__, __LINE__);
		$this->oEventMgr = Container::getDependency('aether.boot.eventmanager');
		//$oEventMgr->addEvent(new Event("terminal.command", "output", [1,1,'foooooooo']));
		
		//var_dump($oEvent);
		// output
		switch ($oEvent->getEvent())
		{
			case 'execute':
			{
				$this->onExecute($oEvent->SessionId, (int) $oEvent->ExecutionId, $oEvent->Command);
				break;
			}

			//ignored events
			case 'output':
			{
				break;
			}
			default:
			{
				$this->oEventMgr->addEvent(new Event("terminal.sys", "output", ["Output" => 'i do not know this event']));
				break;
			}
		}
	}
	/**
	 * onExecute
	 *
	 * Undocumented function long description
	 *
	 * @param Type $var Description
	 * @return type
	 * @throws conditon
	 **/
	public function onExecute(int $iSessionId, int $iExecutionId, string $sCmd)
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __FUNCTION__, __LINE__);
		//if($sCmd)
		// just an echo for now
		$this->oEventMgr->addEvent(new Event("terminal.sys", "output",
										["Output" => print_r("This is some output for $sCmd!", true)]));
		return;
	}

	public function getLocalACNCommands(Type $var = null)
	{
		# code...
	}
}
