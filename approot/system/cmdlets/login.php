<?php
/**
 * Login Cmdlet
 * $Id$
 *
 * DESCRIPTION
 *  PHPInfo Cmdlet for WACC
 *
 * @link https://nxsys.org/spaces/wacc
 * @package WACC\System
 * @copyright Copyright 2013-2015 Nexus Systems, Inc.
 * @license https://nxsys.org/spaces/wacc/wiki/License
 * Please see the license.txt file or the url above for full copyright and
 * license terms.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

namespace Wacc\System\Cmdlets;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console as sfConsole;
use \Wacc\System as WaccSystem;


$sWACC_VER=WaccSystem\WaccApp::APP_VERSION;
define('LOGIN_HELP',<<<EOH
Login Utility V$sWACC_VER


See: <link></link>
EOH
	   );

/**
 * Login Cmdlet
 *
 *
 */
class loginCmdlet extends WaccSystem\Cmdlet
{
	/** @var bool Allows cmdlet to be used without logon **/
	const ALLOW_ANON=true;

	/** @var InputInterface */
	private $oIn;

	/** @var OutputInterface */
	private $oPut;

	public function configure()
	{
        $this
            ->setName('login')
				->setAliases(array('login','logout','exit',
								   'logon','logoff'))
            ->setDescription('Login Utility V'.WaccSystem\WaccApp::APP_VERSION)
            ->setHelp(LOGIN_HELP)
			->setDefinition(array(
                new InputArgument('user', InputArgument::OPTIONAL, 'Username'),
                new InputArgument('pass', InputArgument::OPTIONAL, 'Password or hash'),
				new InputOption('method','m',InputOption::VALUE_REQUIRED,
								'hash or openid')
            ));
	}

	public function execute(InputInterface $oIn, OutputInterface $oPut)
	{
		$this->oIn=&$oIn;
		$this->oPut=&$oPut;

		$ret=null;
		$sMode=$oIn->getFirstArgument();
		switch($sMode)
		{
			case 'login':
			case 'logon':
			{
				if(!$oIn->getArgument('user') || !$oIn->getArgument('pass'))
				{
					$oPut->writeln('You must provide a username and password (credential string)');
					return;
				}
				$ret=$this->login();
				if(!$this->getLastException())
				{
					WaccSystem\WaccApp::getInstance()->runCommand('motd post');
				}
				break;
			}
			case 'logout':
			case 'logoff':
			case 'exit':
			default:
			{
				$this->logout();
				$oPut->writeln('<info>You have been logged out.</info>');
				WaccSystem\WaccApp::getInstance()->runCommand('motd');
			}
		}
		return $ret;
	}

	public function login()
	{
		$fGateStart=microtime(true);


		$sUserName=$this->oIn->getArgument('user');
		$sPassCred=$this->oIn->getArgument('pass');
		$sLoginMethod='plaintext';
		if($this->oIn->getOption('method'))
		{
			$sLoginMethod=$this->oIn->getOption('method');
		}
		switch($sLoginMethod)
		{
			case 'plaintext':
			{
				//@todo query host/local cred store
				$aUsers=array(
					'guest',
					'sysdev',
					'sysadmin',
					'flynn'
				);
				if(!in_array($sUserName, $aUsers))
				{
					//fail....
					$sFailMsg="Authentication Falied, I don't know who $sUserName is.";
					$this->oPut->writeln("<error>$sFailMsg</error>");
					$this->registerException(new \RuntimeException($sFailMsg,401,
																   $this->getLastException()));
					return 401;
				}
				break;
			}
			case 'hash':
			case 'htpasswd':
			case 'openid':
			case 'ldap':
			default:
			{
				// @todo
				throw new \LogicException('Unimplemented!');
			}
		}

		WaccSystem\SessionManager::set('wacc:is_authed', true);
		WaccSystem\SessionManager::set('wacc:authed_user',$sUserName);
		$this->oPut->writeln("<info>Logged in $sUserName.</info>");
		usleep(($fGateStart+3000.0)-microtime(true));
	}

	public function logout()
	{
		WaccSystem\SessionManager::set('wacc:is_authed', false);
	}

	private function doAuththentication($sUsernamne, $mCredentials)
	{}
}