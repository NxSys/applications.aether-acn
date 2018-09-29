<?php
/**
 * wacc.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Connector for WACC
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

chdir(dirname(__FILE__)); //jump from out of the webroot

require_once 'system/libs/silex.phar';
//require_once 'system/libs/symfony-Console/Application.php';

require_once 'system/src/Wacc/System/WaccApp.php';

spl_autoload_register(array('Wacc\System\WaccApp','Autoload'));
\Wacc\System\WaccApp::loadConfig();
//pre parse cmdlets, perhaps compile them all to a cache file

//int dsvfs?

//setup app

// ready to run
	//the constructor runs and app->run()s after this unless....
if(php_sapi_name()=='cli')
{
	//then no index, just us
	$o_Application=\Wacc\System\WaccApp::getInstance();

	$o_Application->oSfConsole->run();
}
