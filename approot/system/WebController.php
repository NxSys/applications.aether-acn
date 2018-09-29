<?php
/**
 * WaccApp.php
 * $Id$
 *
 * DESCRIPTION
 *  Back Controler for WACC
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

use Symfony\Component\HttpFoundation\Response;

class WebController
{
	public $oWaccApp;

	/**
	 * @var Silex\Application
	 */
	public $oSilexApplication;
	/**
	 * @var Symfony\Component\HttpFoundation\Response
	 */
	public $oResponse;
	public function __construct(\Silex\Application $oApp)
	{
		//L0LC0D3Z
		//$this->oWaccApp=WaccApp::getInstance();

		//register twig
		require_once __DIR__.'/libs/Twig/Autoloader.php';
		\Twig_Autoloader::register();
		$oApp->register(new \Silex\Provider\TwigServiceProvider(),
						array(
							'twig.path'       => __DIR__.'/templates',
							'twig.class_path' => __DIR__.'/libs/Twig',
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
		//@todo this is necessary if silex does not take care of it on its own
		//$s_Command = rawurldecode($s_Command);

		# WaccApp does the actual processing
		$WaccApp = WaccApp::getInstance();

		# @todo: load the session

		# run the command
		$a_Response = $WaccApp->runCommand($s_Command);

		//$this->oResponse->setContent(($a_Response));
		$this->oResponse->setContent(json_encode($a_Response));

		return $this->oResponse;
	}
}
