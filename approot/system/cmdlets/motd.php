<?php
/**
 * Login Banner (MotD) Cmdlet
 * $Id$
 *
 * DESCRIPTION
 *  PHPInfo Cmdlet for WACC
 *
 * @link https://f2dev.com/prjs/wacc
 * @package WACC
 * @subpackage System
 * @copyright Copyright 2013 F2 Developments, Inc.
 * @license http://f2dev.com/prjs/wacc/license.html
 * Please see the license.txt file or the url above for full copyright and license information.
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
define('MOTD_HELP',<<<EOH
Login Banner (Message of the Day) Utility V$sWACC_VER



See: <link></link>
EOH
	   );

/**
 * MotD Cmdlet
 *
 *
 */
class motdCmdlet extends WaccSystem\Cmdlet
{
	const ALLOW_ANON=true;

	public function configure()
	{
        $this
            ->setName('motd')
			//->setAliases(array('banner'))
            ->setDescription('Login Banner Utility V'.WaccSystem\WaccApp::APP_VERSION)
            ->setHelp(MOTD_HELP)
			->setDefinition(array(
                new InputArgument('msg', InputArgument::OPTIONAL, 'Which Message', 'prelogin')
            ));
	}

	public function execute(InputInterface $oIn, OutputInterface $oPut)
	{
		$sMessage='';
		switch($oIn->getArgument('msg'))
		{
			case 'post':
			{
				$sMessage=$this->renderMsg(WaccSystem\WaccApp::getConfig('site.banner.post'));
				break;
			}
			case 'prelogin':
			default:
			{
				$sMessage=$this->renderMsg(WaccSystem\WaccApp::getConfig('site.banner'));
			}
		}
		$oPut->writeln($sMessage);
		return;
	}

	private function renderMsg($message)
	{
		$aTokens=array();
		$aTokens['%user']=WaccSystem\SessionManager::get('wacc:authed_user');
		$aTokens['%system']=gethostname();
		$aTokens['%last-logintime']=date('r');
		@$aTokens['%REMOTE_HOST']=$_SERVER['REMOTE_HOST'];
		$aTokens['\n']="\n";
		$aTokens['\t']="\t";
		//$aTokens['']=;
		return strtr($message,$aTokens);
	}
}