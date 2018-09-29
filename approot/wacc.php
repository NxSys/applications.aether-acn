<?php
/**
 * wacc.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Connector for WACC
 *
 * @package WACC
 * @subpackage System
 * @copyright © 2012-20XX F2 Developments, Inc. All rights reserved.
 * @license http://f2dev.com/prjs/prj/lic
 *
 * @author Chris Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

chdir(dirname(__FILE__)); //jump from out of the webroot

require_once 'system/libs/silex.phar';
require_once 'system/libs/symfony-Console/Application.php';

require_once 'system/WaccApp.php';

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
