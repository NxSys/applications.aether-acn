<?php
/**
 * session.php
 * $Id$
 *
 * DESCRIPTION
 *  session Cmdlet for WACC
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

use \Wacc\System as WaccSystem;

$sWACC_VER=WaccSystem\WaccApp::APP_VERSION;
define('SQL_HELP',<<<EOH
Session Utility V$sWACC_VER
Outputs information about PHP's configuration
See: <link>http://php.net/manual/en/function.phpinfo.php</link>
\t
EOH
	   );

class sqlCmdlet extends WaccSystem\Cmdlet
{
	public function configure()
	{
        $this
            ->setName('sql')
            ->setDescription('SQL Query utility V'.WaccSystem\WaccApp::APP_VERSION)
            ->setHelp('SQL');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{

	}
}
