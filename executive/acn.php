<?php
/**
 * $BaseName$
 * $Id$
 *
 * DESCRIPTION
 *  Exec Entrypoint for ACNs
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

////////////////////////////////////////////////////////////////////////////////
///////////////////////////  FOR THE AESH APP  /////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// const PHAR_INSTALLED=TRUE
const PHAR_INSTALLED=FALSE;
////////////////////////////////////////////////////////////////////////////////

//Domestic Namespace
use NxSys\Frameworks\Aether,
	NxSys\Library\Bridges\sfConsole\ToolApplication;

//Framework Namespace
use Symfony\Component\Console as sfConsole;
use NxSys\Core\ExtensibleSystemClasses as CoreEsc;

//start
if (PHAR_INSTALLED)
{
	require_once 'phar://Aether-ACN.phar/src/Common.php';
} 
else
{
	require_once '../src/Common.php';
}

if (!defined('PHAR_NAME'))
{
	return ConsoleMain($argc, $argv);
}

function ConsoleMain($argc, $argv): integer
{
	//our working dir is ./executive
	#$sOldDir=chdir(dirname(__FILE__)); //jump from out of the webroot

	//make sure we can find\load the SDK...

	//setup app?
	$oApp=new Aether\Utility\InvocationWrapper\RceCommand;

	// ready to run
	$o_Application=new ToolApplication($oApp, APP_NAME, APP_VERSION, basename(__FILE__, '.php'));
	return $o_Application->run(new sfConsole\Input\ArgvInput($argv));
}