<?php
/**
 * wacc.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Connector for WACC
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
