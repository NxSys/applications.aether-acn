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

require_once 'Bootloader.php';

//Domestic Namespace
use NxSys\Frameworks\Aether,
F2Dev\Utils\ToolApplication;

//Framework Namespace
use Symfony\Component\Console as sfConsole;

if (!defined('PHAR_NAME'))
{
	return ConsoleMain($argc, $argv);
}

function ConsoleMain($argc, $argv): integer
{

	chdir(dirname(__FILE__)); //jump from out of the webroot

	//pre parse cmdlets, perhaps compile them all to a cache file

	//int dsvfs?

	//setup app
	// if(php_sapi_name()=='cli')
	// 	$o_Application=\Wacc\System\WaccApp::getInstance()
	$oApp=new Aether\Utility\InvocationWrapper\AeshCommand;

	// ready to run
	// 	$o_Application->oSfConsole->run()
	$o_Application=new ToolApplication($oApp, APP_NAME, APP_VERSION, basename(__FILE__, '.php'));
	return $o_Application->run(new sfConsole\Input\ArgvInput(SplFixedArray::fromArray($argv)));
}
