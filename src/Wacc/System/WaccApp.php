<?php
/**
 * WaccApp.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Controller for Web Application Command Console
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

use \Symfony\Component\Console as sfConsole;

class WaccApp
{
	const APP_NAMESPACE='Wacc';
	const APP_NAME='Web Application Command Console';
	const APP_VERSION='0.5.0-TRUNK';

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

	static $oSystemServices;


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
		if($this->getConfig('site.debug')!==null)
		{
			$this->oSilex['debug']=(bool)$this->getConfig('site.debug');
		}

		//load WebController
		$oWebCntrl=new WebController($this->oSilex);

		//register silex route endpoints

		//the 'Index'
		$this->oSilex->get('/',
						   array($oWebCntrl, 'getWebConsole'));

		//for simple cmds via GET, **beware encoding issues!**
		$this->oSilex->get('/docmd/{s_SID}/{s_Command}',
						   array($oWebCntrl, 'processCommand'));

		//the main command handling route, POST only
		$this->oSilex->post('/docmd',
							array($oWebCntrl, 'processPostedCommand'));
		//
		$this->oSilex->get('/system/loadTemplate/{sTemplateName}',
							array($oWebCntrl, 'getSysUiTemplate'));

		$oSysContainer=array();
		//$oSysContainer['FSHandle']=FS\OpenVFSManager;
		//$oSysContainer['ConfigManager']=new;

		self::$oSystemServices=$oSysContainer;
		//$config = new \Doctrine\DBAL\Configuration();
	}

	public function getSystemContainer()
	{
		return self::$oSystemServices;
	}

	public function getFSHandle()
	{
		return FS\FSManager::getNewFSHandle();
	}
	public function getConfigManager()
	{

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
								  APP_BASE_DIR.DIRECTORY_SEPARATOR
								  .'cmdlets'.DIRECTORY_SEPARATOR
								  .'system'
								 );
		$sUserCmdPath=realpath(
								  APP_BASE_DIR.DIRECTORY_SEPARATOR
								  .'cmdlets'.DIRECTORY_SEPARATOR
								  .'local'
								 );
		//read list of commands from config
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
			'about',
			'login',
			'motd',
			'phpinfo',
			'ping',
			'session',
			'sql',
			'type'
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
				throw new \RuntimeException('Can not load user cmdlet from: '.$sCmdPath);
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
		$sFrameworkPath=getcwd().DIRECTORY_SEPARATOR.'system';

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
			.str_replace('Wacc\System\\', null, $sClassName).'.php';
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

	/**
	 * Loads the config from the ini file
	 * @return void
	 */
	static function loadConfig()
	{
		self::$aConfig=parse_ini_file('config.sample.ini',true);
	}

	/**
	 * Loads a config key
	 *
	 * @param string $sKey Config key, eg 'sitename'
	 *
	 * @return string config value
	 */
	static function getConfig($sKey)
	{
		// @todo error checking
		if(array_key_exists($sKey,self::$aConfig['WACC']))
		{
			return self::$aConfig['WACC'][$sKey];
		}
		return null;
	}

	/**
	 * Runs the WaccApp
	 *
	 * Figures out what runtime to use. Is called from the FC normally and ATM only calls silex
	 *
	 * @returns void
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
	 * Can be used to run subcommands from exsisting cmdlets,
	 *  (tho this is sometimes contraindicated)
	 *
	 * @param string|sfConsole\Input\ArgvInput $s_Command The command string to run
	 * @param sfConsole\Output\Output $oOutputBuffer A  output buffer to use
	 *
	 * @return array The command's return code and output (stored at 'code' and 'output' respectively)
	 */
	public function runCommand($s_Command, $oOutputBuffer=null)
	{
		static $oLocalOutBuff;
		if(!$oOutputBuffer) //if you didn't give me one
		{
			if(!$oLocalOutBuff) //if i don't have a local buffer either....
			{
				//warning i have no way to output things....
				//i'm now assuming im on the CLI sapi
				$oLocalOutBuff=new sfConsole\Output\ConsoleOutput;
			}
			//then set the current out buffer to my local buffer
			$oOutputBuffer=$oLocalOutBuff;
		}
		$oLocalOutBuff=$oOutputBuffer; //keep a copy of buffer incase we don't get one again, until we get a different one

		/* @var sfConsole\Input\StringInput or maybe a string */
		$oCmdInput=$s_Command;

		// lets make sure $oCmdInput always has an input object
		if(!$s_Command instanceof sfConsole\Input\Input)
		{
			$oCmdInput=new sfConsole\Input\StringInput($s_Command);
		}

		// @todo: check for an CmdChain array
		if($oCmdInput->hasParameterOption('|') && is_string($s_Command))
		{
			//cmd chaining...
			//explode/implode?
			$oChainProcessor=new ChainedStringLinker;
			$aCmdChain=$oChainProcessor->process($s_Command, '|');
			foreach($aCmdChain as $aCmdArray)
			{
				$sCmdLine=implode(' ',$aCmdArray);
				$this->runCommand(new sfConsole\Input\StringInput($sCmdLine));
			}
			//aaand we're done
			return 0;
		}

		//go get the cmdlet destined to run
		try
		{
			$oCurrentCmdlet=$this->findCommand($oCmdInput->getFirstArgument());
		}
		catch(\InvalidArgumentException $e)
		{
			$oOutputBuffer->writeln('<error>Error processing command.</error>');
			$oOutputBuffer->writeln("<error>\t{$e->getMessage()}</error>");
			return 10;
		}

		//the current session has not authenticated
		if(!SessionManager::get('wacc:is_authed'))
		{
			//check to see if the command allows anon
			if(!defined(get_class($oCurrentCmdlet).'::ALLOW_ANON')
			   || !$oCurrentCmdlet::ALLOW_ANON)
			{
				// then fail
				$oOutputBuffer->writeln('<error>Authentication Required</error>');
				$oOutputBuffer->writeln('<info>Try typing LOGIN</info>');
				return 401;
			}
		}

		//set verbosity high if in debug mode
		if($this->isDebugModeActive())
		{
			$oOutputBuffer->setVerbosity(sfConsole\Output\OutputInterface::VERBOSITY_VERBOSE);
		}

		//lets do it...
		$ret=$this->oSfConsole->run($oCmdInput, $oOutputBuffer);

		//and now, what have we done
		if($oCurrentCmdlet instanceof \Wacc\System\Cmdlet
		   && $e=$oCurrentCmdlet->getLastException())
		{
			$oOutputBuffer->write("<error>A runtime error occurred while running the command.</error>");
			if($this->isDebugModeActive())
			{
				$oOutputBuffer->writeln(' Stack trace follows:');
				$this->oSfConsole->renderException($e,$oOutputBuffer);
			}
			else
			{
				//if no message, display ex class name
				$sMsg=$e->getMessage()?$e->getMessage():get_class($e);

				//display ex code, if there is one
				$sMsg.=$e->getCode()?sprintf(' (%d)',$e->getCode()):'';
				$oOutputBuffer->writeln("\n<error>\t$sMsg</error>");
			}
			$ret=$e->getCode();
		}

		//@todo check to see that error was not handled from sfConsole
		//@todo output chaining, feat #51
		return $ret;
	}

	/**
	 * Web ingress for silex
	 *
	 * Iniallizes BasicHtmlOutput buffer and returns an array for the Console Client
	 * Must be called once per request
	 *
	 * @param string $s_Command cmd to run
	 *
	 * @return array Array with a return code and buffer contents
	 */
	public function runWebCommand($s_Command)
	{
		static $bRunOnce;
		if($bRunOnce)
		{
			throw new \RuntimeException('Can not runWebCommand more than once');
		}
		$bRunOnce=true;

		//@todo command history and output caching

		//commented so see have access to the "raw" string
		//$oCmdString=new sfConsole\Input\StringInput($s_Command);
		//var_dump($oCmdString->getFirstArgument());
		$oWebOutput=new BasicHtmlOutput();
		//@todo check for decoration flags

		$ret=$this->runCommand($s_Command, $oWebOutput);

		//$ret 'might' be an object...
		return array('code'=>$ret,'output'=>$oWebOutput->getOutput());
	}

	/**
	 * Returns a Cmdlet from the console application
	 *
	 * @throws \InvalidArgumentException When sfConsole can't find the command
	 * @return \Wacc\System\Cmdlet
	 */
	public function findCommand($sCommandName)
	{
		// @todo dynamically loaded commands with class caches
		return $this->oSfConsole->find($sCommandName);

	}

	/**
	 * Returns the status of the debug flag
	 *
	 * @returns bool
	 */
	static public function isDebugModeActive()
	{
		if(SessionManager::get('wacc:site.debug')!==null)
		{
			return (boolean)SessionManager::get('wacc:site.debug');
		}
		return (boolean)self::getConfig('site.debug');
	}
}

class WaccSessionObject
{
    public $history;
    public $output;
    public $prompt;
	public $environment;
}
