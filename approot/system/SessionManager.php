<?php
/**
 * WaccApp.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Controler for Web Application Command Console
 *
 * @link https://f2dev.com/prjs/wacc
 * @package WACC
 * @subpackage System
 * @license http://f2dev.com/prjs/wacc/license.html
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2013 F2 Developments, Inc.
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
}