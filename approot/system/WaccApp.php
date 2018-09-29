<?php
/**
 * WaccApp.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Controler for Web Application Command Console
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
namespace Wacc\System;

use \Symfony\Component\Console as sfConsole;

class WaccApp
{
	const APP_NAMESPACE='Wacc';
	const APP_NAME='Web Application Command Console';
	const APP_VERSION='0.1.$Revision$';

	/**
	 * @var string App name ()
	 */
	//public $sApp_Name='WACC';

	/**
	 * @var \Silex\Application
	 */
	public $oSilex;

	/**
	 * @var Symfony\Component\Console\Application
	 */
	public $oSfConsole;

	/**
	 * @var array list of avail commands
	 */
	public $aCommands=array();

	/**
	 * @var WaccApp The WaccApp
	 */
	static $instance;


	static $aConfig;


	public function __construct()
	{
		$this->oSfConsole=new sfConsole\Application(self::APP_NAME,self::APP_VERSION);
		$this->oSfConsole->setAutoExit(false);
		//$this->oSfConsole->setCatchExceptions(false);

		$this->oSilex=new \Silex\Application();

		//load all cmdlets
		$this->loadCommands();

        //loader system commands
		$commands=$this->aCommands['system'];
		foreach ($commands as $aCommand)
		{
			//echo "Registering System Command {$aCommand['classname']}...\n";
            $this->oSfConsole->add(new $aCommand['classname']);
        }

		//load user commands
		$commands=$this->aCommands['user'];
		foreach ($commands as $aCommand)
		{
			//echo "Registering User Command {$aCommand['classname']}...\n";
            $this->oSfConsole->add(new $aCommand['classname']);
        }

		//check for debug mode
		if(isset(self::$aConfig['WACC']['site.debug']))
		{
			$this->oSilex['debug']=(bool)self::$aConfig['WACC']['site.debug'];
		}

		//load WebController
		$oWebCntrl=new WebController($this->oSilex);

		//register silex route endpoints
		$this->oSilex->get('/',
			function () use ($oWebCntrl)
			{
				return $oWebCntrl->getWebConsole();
			}
		);

		$this->oSilex->match('/docmd/{sid}/{cmd}',
			function ($sid, $cmd) use ($oWebCntrl)
			{
				return $oWebCntrl->processCommand($sid, $cmd);
			}
		);

	}

	/**
	 * loadCommands()
	 *
	 * @todo: use a descent cache strategy
	 *
	 *
	 * @return void
	 */
	public function loadCommands()
	{
		if(isset(self::$aConfig['cache_commands']) && self::$aConfig['cache_commands'])
		{
			//@todo caching
			return;
		}
		$sSystemCmdPath=realpath(
								  dirname(__FILE__).DIRECTORY_SEPARATOR
								  .'cmdlets' //sibling dir
								 );
		$sUserCmdPath=realpath(
								  dirname(__FILE__).DIRECTORY_SEPARATOR
								  .'..'.DIRECTORY_SEPARATOR
								  .'cmdlets' //up a lvl
								 );
		//read list of commands from confg
		$aUserConfigCmds=self::$aConfig['enable']['user.cmds'];
		$aUserCmds=array();
		foreach($aUserConfigCmds as $sUserCmd)
		{
			$aUserCmds[$sUserCmd]=array
			(
				'name' => $sUserCmd,
				'classname' => "\Wacc\User\Cmdlets\\{$sUserCmd}Cmdlet"
				// etc
			);
		}

		//get list of system commands
		$aSysCmds=array(
			'about'/*,
			... */
		);
		$aSystemCmds=array();
		foreach($aSysCmds as $sSysCmd)
		{
			$aSystemCmds[$sSysCmd]=array
			(
				'name' => $sSysCmd,
				'classname' => "\Wacc\System\Cmdlets\\{$sSysCmd}Cmdlet"
			);
		}


		//merge
		$aCommandList['user']=$aUserCmds;
		$aCommandList['system']=$aSystemCmds;

		//@todo impl disable cmds
		//disable commands (only system ATM)
		//foreach(self::$aConfig['disable']['sys.cmds'] as $sCmd)
		//{
		//	unset($aCommandList[$sCmd]);
		//}

		//now include them all from

		//load user commands
		foreach($aUserCmds as $sUserCmd)
		{
			$sUserCmdName=$sUserCmd['name'];
			$sCmdPath=$sUserCmdPath.DIRECTORY_SEPARATOR.$sUserCmdName;
			if(is_dir($sCmdPath))
			{
				$sLocation=$sCmdPath.DIRECTORY_SEPARATOR.$sUserCmdName.'.php';
			}
			elseif(is_file($sCmdPath.'.php'))
			{
				$sLocation=$sCmdPath.'.php';
			}
			elseif(is_file($sCmdPath.'.phar'))
			{
				$sLocation=$sCmdPath.'.phar';
			}
			else
			{
				throw new RuntimeException('Can not load user cmdlet from: '.$sLocation);
			}
			$aCommandList['user'][$sUserCmdName]['location']=$sLocation;
			require_once $sLocation;
		}

		//load system commands
		foreach($aSystemCmds as $sSystemCmd)
		{
			$sCmdPath=$sSystemCmdPath.DIRECTORY_SEPARATOR.$sSystemCmd['name'];
			if(is_dir($sCmdPath))
			{
				require_once $sCmdPath.DIRECTORY_SEPARATOR.$sSystemCmd['name'].'.php';
			}
			else
			{
				require_once $sCmdPath.'.php';
			}
			//echo "Loaded SystemCmd: {$sSystemCmd['name']}\n";
		}

		$this->aCommands=$aCommandList;
		//var_dump($aCommandList);
	}

	/**
	 * Autoloader for every class thats not a user cmdlet
	 *
	 * @param string Class Name
	 */
	public static function autoload($sClassName)
	{
		//only autoload \Wacc\System
		$sFrameworkPath=dirname(__FILE__);

		//SFConsole
		//if(strpos($sClassName,self::APP_NAMESPACE)===false)
		//{
		//	//SEP
		//	return false;
		//}

		foreach(self::$aConfig['WACC']['nsmapping'] as $sNameSpace => $sLibDir)
		{

			if(strpos($sClassName,$sNameSpace)===0) //0 or false because if its not 0 then shenanigans
			{
				// match!
				$iLen=strlen($sNameSpace);
				$sProposedClassFilePath=
					$sFrameworkPath.DIRECTORY_SEPARATOR
					.'libs'.DIRECTORY_SEPARATOR
					.$sLibDir.DIRECTORY_SEPARATOR
					.str_replace('\\',DIRECTORY_SEPARATOR,substr($sClassName,$iLen+1)).'.php';
				//var_dump($sProposedClassFilePath);
				if(file_exists($sProposedClassFilePath))
				{

					require_once $sProposedClassFilePath;
					return true;
				}
			}
			//else... what?
		}

		$sProposedClassFilePath=$sFrameworkPath.DIRECTORY_SEPARATOR
			.str_replace('Wacc\System\\',null,$sClassName).'.php';
		//var_dump($sProposedClassFilePath);
		if(!file_exists($sProposedClassFilePath))
		{
			return false;
		}


		//todo: classpaths... if classes ever get... crazy
		//if use class paths
			//load path from file

		require_once $sProposedClassFilePath;
		return true;
	}

	/**
	 *
	 * Yay Factory
	 *
	 * @return WaccApp *The* WaccApp
	 */
	static public function getInstance()
	{
		if(!self::$instance)
		{
			self::$instance=new self;
		}
		return self::$instance;
	}

	static function loadConfig()
	{
		self::$aConfig=parse_ini_file('config.sample.ini',true);
	}

	/**
	 * Runs the WaccApp
	 *
	 * Is called from the FC
	 *
	 * returns void
	 */
	public function doRequest()
	{
		if(php_sapi_name()!='cli')
		{
			// call the silex router
			//start of request
			$this->oSilex->run();
			//request is completed
			return;
		}

		//else this is cli, we should call the requested module and leave

		return;
	}

	/**
	 * Runs a command
	 *
	 * @param string $s_Command The command string to run
	 *
	 * @return array The command's return code and output (stored at 'code' and 'output' respectively)
	 */
	public function runCommand($s_Command)
	{
		//@todo command history and output caching
		//

		$oCmdString=new sfConsole\Input\StringInput($s_Command);

		$oWebOutput=new BasicHtmlOutput();
		//@todo check for decoration flags

		$ret=$this->oSfConsole->run($oCmdString,$oWebOutput);
		if($ret!==0
		   && false) //@todo check to see that error was not handled from sfConsole
		{
			throw new \RuntimeException('An error occured while executing the command');
		}

		//@todo output chaining, feat #51
		return array('code'=>$ret,'output'=>$oWebOutput->getOutput());
	}
}

class WaccSessionObject
{
    public $history;
    public $output;
    public $prompt;
	public $environment;
}
