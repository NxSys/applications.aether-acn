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
namespace NxSys\Frameworks\Aether\Utility\InvocationWrapper;

/** Local Project Dependencies **/
use NxSys\Frameworks\Aether,
	NxSys\Frameworks\Aether\Listener\RceServiceListener;

/** Framework Dependencies **/
use Symfony\Component\Console as sfConsole;

/** Library Dependencies **/
use Amp\Socket as ListenServer;
use Kraken;

/**
 * Undocumented class
 *
 * Why does this exist? What does this do?
 *
 * @throws NxSys\Frameworks\Aether\IException Well, does it?
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 */
 class RceCommand extends sfConsole\Command\Command
 {
	public function configure()
	{
		$this->setName('Rce');
	}

	public function execute($oIn, $oPut)
	{
		//init
			//load local svcs
		//output std head & ver stanzas
		$oPut->write('RCE Def Port is '.RceServiceListener::RCE_DEFAULT_PORT);

		//do what the user asked

		//do serve
		$this->execService();
	}

	protected function execService(array $sAddresses=[], int $iPort=RceServiceListener::RCE_DEFAULT_PORT): bool
	{
		//load config
		//load logger
		//allocate resources?

		$oMgmtOpsContainer=new Kraken\Runtime\RuntimeContainer('rce','mgmt', 'mgmt');
		$oServiceListenerContainer=new Kraken\Runtime\RuntimeContainer('rce', 'svc', 'svc');
		$oCmdEnvContainer=new Kraken\Runtime\RuntimeContainer('rce', 'cmdenv', 'cmdenv');

		$oServiceListenerContainer->onStart(function() {
			echo 'svc start';
		});

		$oServiceListenerContainer->onStop(function() {
			echo 'svc stop';
		});
		$oServiceListenerContainer->getManager()
		->createThread('listen', 'listen', null)
		->done(function() {
			echo "Thread has been created!\n";
		});
		$oServiceListenerContainer->start();
		sleep(7);
		var_dump($oServiceListenerContainer->getState());
		$oServiceListenerContainer->fail(new \RuntimeException, null);
		return false;
	}

	public function runLoop()
	{
		# code...
	}

	protected function loadConfig()// : ConfigStoreCollection
	{
		# code...
	}

}
