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
		$this->callRemoteCommand($iExecutionId, $sCmd);
		$this->oEventMgr->addEvent(new Event("terminal.sys", "output",
								   ["Output" => print_r("This is some output for $sCmd!", true)]));
		return;
	}

	public function callRemoteCommand(int $iExecutionId, string $sCmd): string
	{

		//which env is this good for?

			//set staus as pending remoting to rce x

		//get rce comm details
		$sRceLocator='http://127.0.0.1:8335';

		//package...

		//...and send
		$oRceService=new \Zend\Soap\Client(null, ['location'=> $sRceLocator, 'uri' => '']);
		$ret=$oRceService->sendEvent('INVALID!INVALID!INVALID!INVALID!', //session key
									 'rce.submissions',
									 'newCommand',
									 ['iExecutionId' => $iExecutionId,
									 'command' => $sCmd]);

		//set exec status as pending start
		return $ret;
	}

	public function getLocalACNCommands(Type $var = null)
	{
		# code...
	}
}
