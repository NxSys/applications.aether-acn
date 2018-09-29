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
namespace NxSys\Frameworks\Aether\Execution\Agents;

/** Local Project Dependencies **/
use NxSys\Frameworks\Aether;

/** Framework Dependencies **/


/** Library Dependencies **/
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;

/**
 * Undocumented class
 *
 * Why does this exist? What does this do?
 *
 * @throws NxSys\Frameworks\Aether\IException Well, does it?
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 */
 interface IAgent extends CoreEsc\pthreads\IWorker
 {

 }
