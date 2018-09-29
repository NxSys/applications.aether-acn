<?php
/**
 * Type Cmdlet
 * $Id$
 *
 * Dumps a file with line numbers
 *
 * @link http://f2dev.com/prjs/wacc
 * @package Wacc
 * @subpackage System
 * @author Nate Timmons <ntimmons@nexussystemsinc.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 *
 * @copyright Copyright 2013 F2 Developments, Inc.
 * @license http://f2dev.com/prjs/wacc/license.html
 * Please see the license.txt file or the url above for full copyright and license information.
 */
namespace Wacc\System\Cmdlets;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

use \Wacc\System as WaccSystem;

class typeCmdlet extends WaccSystem\Cmdlet
{
	public function configure()
	{
	    $this
                ->setDefinition(array(
                    new InputArgument('fileName', InputArgument::REQUIRED, 'The filename to display the contents of')
                
                ))
                ->setName('type')
                ->setDescription('Displays the contents of a file, printing line numbers')
                ->setHelp('...');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
            $fileLines = file($input->getArgument('fileName'));
            $justifyCt = floor(log(count($fileLines),10))+1;
	    //echo $justifyCt;
	    foreach ($fileLines as $lineNum => $line)
            {
		$format = "%".$justifyCt."d";
                $output->write(sprintf($format,$lineNum));
                $output->write(" ".$line);
            }
	}
}
?>