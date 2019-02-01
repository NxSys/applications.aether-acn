<?php

namespace NxSys\Applications\Aether\ACN\Handlers;

use NxSys\Toolkits\Aether\SDK\Core\Boot\Event\Event;
use NxSys\Toolkits\Aether\SDK\Core\Boot\Container;

use SoapFault;

/**
 * @Channels terminal|terminal.command|terminal.meta|terminal.sys
 */
class TerminalEndpointHandler
{
	public function handleEvent(Event $oEvent)
	{
		printf(">>>CHECKPOINT %s::%s:%s<<<", __CLASS__, __METHOD__, __LINE__);
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
				$this->oEventMgr->addEvent(new Event("terminal.sys", "output", ["Output" => 'i do not know this event '
																				.sprintf('%s:%s',
																				$oEvent->getChannel(), $oEvent->getEvent())]));
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
		$iExecId=$this->callRemoteCommand($iExecutionId, $sCmd);
		$this->oEventMgr->addEvent(new Event("terminal.sys", "output",
								   ["Output" => print_r("This is some output for $sCmd! - ".$iExecId, true)]));
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
		$oRceService=new \Zend\Soap\Client(null, ['location'=> $sRceLocator,
												  'uri' => 'http://stds.aether.sh/soap',
												  'connection_timeout' => 3]);
		try
		{
			$ret=$oRceService->addEvent('INVALID!INVALID!INVALID!INVALID!', //session key
										'rce.submissions',
										'newCommand',
										['iExecutionId' => $iExecutionId,
										'command' => $sCmd]);
		}
		catch (SoapFault $th)
		{
			sprintf('TEH Error %s:%s',$th->getMessage(), $th->getCode() );
			$this->oEventMgr->addEvent(new Event('sys.error', 'rce-comm-failure', ['msg'=>'Caught a SoapFault: '.$th->getMessage()]));
			$ret=-1;
		}

		//set exec status as pending start
		return $ret;
	}

	public function getLocalACNCommands(Type $var = null)
	{
		# code...
	}
}
