<?php
/**
 * WaccApp.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Controler for WACC
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

use Silex\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebController
{
	/**
	 * The WaccApp
	 * @var Wacc\System\WaccApp
	 */
	public $oWaccApp;

	/**
	 * The Silex Application
	 * @var Silex\Application
	 */
	public $oSilexApplication;

	/**
	 * The response object use for composing responses to web requests
	 * @var Symfony\Component\HttpFoundation\Response
	 */
	public $oResponse;

	public $bUseJSON;

	public $bIsSessionAuthenticated;

	/**
	 * __constructor
	 *
	 * This registers:
	 *  \Silex\Provider\SessionServiceProvider
	 *  \Silex\Provider\TwigServiceProvider
	 *
	 * @param \Silex\Application $oApp
	 * @return \Symfony\Component\HttpFoundation\Response Response object
	 */
	public function __construct(\Silex\Application &$oApp)
	{
		$this->bUseJSON=true;
		//$this->oWaccApp=WaccApp::getInstance();

		//register Silex's Session 'provider'
		$oApp->register(new \Silex\Provider\SessionServiceProvider());

		//register twig
		require_once './system/libs/Twig/Autoloader.php';
		\Twig_Autoloader::register();
		$oApp->register(new \Silex\Provider\TwigServiceProvider(),
						array(
							'twig.path'       => './system/templates',
							'twig.class_path' => './system/libs/Twig',
							)
					   );

		$this->oSilexApplication=$oApp;

		$this->aPageVars=array(
			'wacc_name'	=>	WaccApp::APP_NAME,
			'wacc_ver'	=>	WaccApp::APP_VERSION,
			'site_name'	=>	WaccApp::$aConfig['WACC']['sitename'],

			null
		);

		$this->oResponse=new Response();
		return;
	}

	/**
	 * Loads the console
	 *
	 * @ return string the console client
	 */
	public function getWebConsole()
	{
		# build/load web client page/container
		$aPageVars=array();
		$s_WebClient='<b>ConsoleHere!</b>';


		$sBody=$this->oSilexApplication['twig']
				->render('WebConsole.twig',
						 $this->aPageVars);
		$this->oResponse->setContent($sBody);
		return $this->oResponse;
	}

	public function doAuthentication()
	{
		$this->oWaccApp->findCommand('login')->login();
	}

	public function verifyAuthentication()
	{
		//return (bool)SessionManager::get('wacc::is_authed');
		//var_dump(SessionManager::get('wacc:is_authed'));
		return true;
	}

	/**
	 * Processes a command from the loaded console
	 *
	 * @param string $s_SID The session identifier
	 * @param string $s_Command The command string to run
	 *
	 * @return string the command output
	 */
	public function processCommand($s_SID, $s_Command)
	{
		//Security Gate
		if(!$this->verifyAuthentication())
		{
			if($this->bUseJSON)
			{
				$a_Response=
					array(
						'code' => '401',
						'output' => 'ERR: Authentication is required'
					);
				$this->oResponse->setContent(json_encode($a_Response));
			}
			else
			{
				if(!WaccApp::isDebugModeActive())
				{
					throw new \Exception('ERR: Authentication is required',401);
				}
				else
				{
					$this->oResponse->setStatusCode(401, 'WACC Authentication is required');
				}
			}
			return $this->oResponse;
		}
		//@todo this is necessary if Silex does not take care of it on its own
		//$s_Command = rawurldecode($s_Command);

		# WaccApp does the actual processing
		$WaccApp = WaccApp::getInstance();

		# @todo: load the session

		# run the command
		$a_Response = $WaccApp->runWebCommand($s_Command);

		if($this->bUseJSON)
		{
			//@todo: (upg silex) use silex's helper function
			//$this->oResponse=$this->oSilexApplication->json($a_Response);
			$this->oResponse->headers->set('Content-Type','application/json');
			$this->oResponse->setContent(json_encode($a_Response));
		}
		else
		{
			$this->oResponse->setContent(sprintf('<pre>%s</pre>',
												 $a_Response['output']));
		}

		return $this->oResponse;
	}

	public function processPostedCommand(Request $oRequest)
	{
		//some things (like cmd-form.html) don't want json output
		if($oRequest->request->get('useraw'))
		{
			$this->bUseJSON=false;
		}
		return $this->processCommand($oRequest->request->get('sid'),
									 $oRequest->request->get('cmd'));
	}

	public function getSysUiTemplates($sTemplatePath)
	{
		//check to see if file exsists
		//fopen and stream back to client
		return $this->oResponse;
	}
}
