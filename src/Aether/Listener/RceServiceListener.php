<?php
/**
 * $BaseName$
 * $Id$
 *
 * DESCRIPTION
 *  A Core file for Aether.sh
 *
 * @link http://nxsys.org/spaces/aether
 * @link https://onx.zulipchat.com
 *
 * @package Aether
 * @subpackage System
 * @license http://nxsys.org/spaces/aether/wiki/license
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2018 Nexus Systems, inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

/** @namespace Native Namespace */
namespace NxSys\Frameworks\Aether\Listener;

/** Local Project Dependencies **/
use NxSys\Frameworks\Aether;

/** Framework Dependencies **/


/** Library Dependencies **/

/**
 * Remote Command Environment Listener Port
 * @see RceServiceListener
 * @var int
 */
const RCE_DEFAULT_PORT=8324;


/**
 * Undocumented class
 *
 * Why does this exist? What does this do?
 *
 * @throws NxSys\Frameworks\Aether\IException Well, does it?
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 */
 class RceServiceListener extends CommonServiceListener
 {
	const RCE_DEFAULT_PORT=RCE_DEFAULT_PORT;

	public function initContainer(Type $var = null)
	{
		# code...
		$c=new Kraken\Runtime\RuntimeContainer;
	}

	public function loop(): bool
	{
		$sListenUri=sprintf('tcp://0.0.0.0:%d',$iPort);
		$oListenServer=ListenServer\listen();

		$oListenStream = $this->createListenerStream($oListenServer);
		foreach ($oListenStream as $oConn)
		{
			if ($oConn == null)
			{
				$oListenStream->send(false);
				return false;
			}
			else
			{	// do something with $client, which is a ServerSocket instance

				// you shouldn't yield here, because that will wait for the yielded promise
				// before accepting another client, see below.
			}
		}
		return true;
	}

	public function createListenerStream(ListenServer\listen $oListener) : \Generator
	{
		$bContinue = True;
		while ($bContinue)
		{
			$bContinue = yield $oListener->accept();
		}
	}

	public function handleConnection()
	{
		# code...
	}

	public function handleCommand()
	{
		# code...
	}
 }
