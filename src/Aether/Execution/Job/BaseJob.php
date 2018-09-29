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
namespace NxSys\Frameworks\Aether\Execution\Job;

/** Local Project Dependencies **/
use NxSys\Frameworks\Aether;

/** Framework Dependencies **/


/** Library Dependencies **/
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;

//....
use Thread;


/**
 * Undocumented class
 *
 * Why does this exist? What does this do?
 *
 * @throws NxSys\Frameworks\Aether\IException Well, does it?
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 */
// abstract class BaseJob extends CoreEsc\pthreads\Thread implements IJob
abstract class BaseJob extends Thread implements IJob
{
	/**
	 * mmmm
	 */
	public function setExternalRoutine(callable $hfRoutineTarget)
	{
		$this->_oTargetObject=(array) $hfRoutineTarget;
	}

	/**
	 * Undocumented function
	 *
	 * @return mixed
	 */
	public function getReturn(): mixed
	{
		//eehh we really should be joined to do this
		static $bIsJoined;
		if(!$bIsJoined)
		{
			$this->join();
			$bIsJoined=true;
		}
		return $this->return;
	}

	public function __tostring(): string
	{
		return (string)$this->getReturn();
	}

}
