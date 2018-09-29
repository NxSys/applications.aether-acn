<?php
/**
 * WaccApp.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Controler for Web Application Command Console
 *
 * @link https://nxsys.org/spaces/wacc
 * @package WACC\System
 * @license https://nxsys.org/spaces/wacc/wiki/License
 * Please see the license.txt file or the url above for full copyright and
 * license terms.
 * @copyright Copyright 2013-2015 Nexus Systems, Inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

namespace Wacc\System;

use Symfony\Component\HttpFoundation\Session\Session as SessionMgr;

/**
 *
 *
 */
class SessionManager
{
	/** @var SilexSessionMgr */
	public $oBackendManager;

	public function __construct()
	{

	}

	public static function get($sName)
	{
		return WaccApp::getInstance()
			->oSilex['session']
			->get($sName);
	}

	public static function getWaccSessId()
	{
		return WaccApp::getInstance()
			->oSilex['session']
			->getId();
	}

	public static function set($sName, $mValue)
	{
		WaccApp::getInstance()
			->oSilex['session']
			->set($sName, $mValue);
	}

	public function remove($sName)
	{
		WaccApp::getInstance()
			->oSilex['session']
			->remove($sName);
	}

	public function all()
	{
		return WaccApp::getInstance()
			->oSilex['session']
			->all();
	}


}