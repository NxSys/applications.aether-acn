<?php
/**
 * phpinfo.php
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

use \Wacc\System as WaccSystem;

$sWACC_VER=WaccSystem\WaccApp::APP_VERSION;
define('PING_HELP', <<<END
Ping Utility V$sWACC_VER
  Its ping. And does what it says on the tin.\n
Pings subject for c counts (default 4) with i interval (default 1.0 seconds).
There is a count max of 20 and interval must be greater than 0.5 and
less than 20 (20 > i > 0.5). /Be kind to your environment!/

Subject formats:
  http://HOST[[:PORT]/[URL]]
\tPings with a HTTP/1.1 request and shows status code.

  tcp://SUBJECT[:PORT]
\tWithout a port, will do an ICMP ping. Otherwise sends 2 bytes and a newline.

More Info:
<link>http://scm.f2dev.net/projects/wacc/wiki/Ping</link>
END
		   );

/**
 * pingCmdlet
 *
 *
 */
class pingCmdlet extends WaccSystem\Cmdlet
{
	public $iTimeoutSecs;

	public function configure()
	{
        $this
            ->setName('ping')
            ->setDescription('Ping Utility V'.WaccSystem\WaccApp::APP_VERSION)
            ->setHelp(PING_HELP)
			->setDefinition(array(
                new InputArgument('subject', InputArgument::OPTIONAL, 'Subject to ping', 'me'),
				new InputOption('count','c', InputOption::VALUE_REQUIRED, 'Number of times to ping.', 4),
                new InputOption('interval', 'i', InputOption::VALUE_REQUIRED, 'Time interval between pings in seconds.', 1.0),
            ));
	}

	public function execute(InputInterface $oIn, OutputInterface $oPut)
	{
		$sDispFormat='Status from %s: %s time=%s';
		$iFailCount=0;
		$iSucceedCount=0;
		$iTimeMax=0;
		$iTimeMin=0;
		$fTimeAvg=0.0;

		$sSubjectName=(string)$oIn->getArgument('subject');
		$iCount=(integer)$oIn->getOption('count');
		$fInterval=(float)$oIn->getOption('interval');

		if("me"==$sSubjectName)
		{
			//web or no?
			//if(PHP_SAPI!=="cli")
			//{
			//	$sHost=php_uname('n');
			//}
			//else
			//{
			//	$sHost=$_SERVER['SERVER_NAME'];
			//}
			$sSubjectName='http://'.gethostname(); //more compatible?
		}

		$aHostInfo=parse_url($sSubjectName);
		//check protocal validity
		if(!is_array($aHostInfo)
		   || !isset($aHostInfo['scheme'])
		   || !isset($aHostInfo['host']))
		{
			$oPut->writeln('<error>'.$sSubjectName.' is an invalid subject.</error>');
			$oPut->writeln('<info>You require at least scheme and host components.</info>');
			return -1;
		}

		//check host validity...
		$sTargetName=$aHostInfo['host'];
		$sHostAddr=gethostbyname($sTargetName);
		if($sHostAddr==$sTargetName)
		{
			//maybe its an ip already...
				//@hack, this funck is wierd... and my fail v6 unit tests :-(
			if(!filter_var($sHostAddr,FILTER_VALIDATE_IP))
			{
				$sMsg='<error>Ping can not resolve the host name %s to a valid ip address.</error>';
				$oPut->writeln(sprintf($sMsg,$sTargetName));
				return -1;
			}
			$sTargetName=gethostbyaddr($sHostAddr);
		}
		//subject resolution
		$oPut->writeln("\nPinging $sTargetName: [$sHostAddr]...");

		$iCount=min($iCount,100);
		$iCount=max($iCount,1);
		$fInterval=min($fInterval,20);
		$fInterval=max($fInterval,0.5);
		for($i=0;$i<$iCount;$i++)
		{
			$iStartTime=microtime(true);
			//try
			//{
			//	$aPingInfo=$this->doPing($sSubjectName);
			//}
			//catch(DoPingException $e)
			//{
			//
			//}
			$aPingInfo=$this->doPing($sSubjectName);

			if($aPingInfo['OK'])
			{
				$oPut->writeln(
					sprintf($sDispFormat,
							$sSubjectName,
							$aPingInfo['message'],
							$aPingInfo['time'].'ms'));
				$iSucceedCount++;
			}
			else
			{
				$oPut->writeln('FAIL: '.$aPingInfo['message']);
				$aPingInfo['time']=0;
				$iFailCount++;
			}

			//init the first time around
			if(0==$i)
			{
				$iTimeMin=$iTimeMax=$fTimeAvg=$aPingInfo['time'];
			}
			else
			{
				$iTimeMin=min($iTimeMin,$aPingInfo['time']);
				$iTimeMax=max($iTimeMax,$aPingInfo['time']);
				$fTimeAvg=($fTimeAvg+$aPingInfo['time'])/2;
			}
			@time_sleep_until($iStartTime-(microtime(true)-$iStartTime)+$fInterval);
		}
		$sFinalReport=
			"\nPing statistics for $sSubjectName\n"
			.sprintf("    Packets: Sent = $iCount, Received = $iSucceedCount, Lost = $iFailCount (%d%% loss),\n",
					 round(($iFailCount/$iCount)*100))
			."Approximate round trip times in milli-seconds:\n"
			."    Minimum = {$iTimeMin}ms, Maximum = {$iTimeMax}ms, Average = {$fTimeAvg}ms";
		$oPut->writeln($sFinalReport);

		return ($iFailCount>=$iCount)?$iFailCount:0;
	}

	/**
	 * does a single "ping"
	 *
	 * @param string $s_SubjectName Subject/Host name "uri"
	 * @return array ping info
	 */
	private function doPing($s_SubjectName)
	{
		$aInfo=array();
		$iTimeoutSecs=1;

		// @todo
		//$iTimeoutSecs=$this->iTimeoutSecs;

		//should be in top shape by the time we get here
		$aHostInfo=parse_url($s_SubjectName);
		if(!is_array($aHostInfo))
		{
			throw new InvalidArgumentException('Subject is not parsable.');
		}


		$aInfo['OK']=true; //for now
		switch ($aHostInfo['scheme'])
		{
			case 'http':
			{
				$sUri='';
				$iPort=isset($aHostInfo['port'])?$aHostInfo['port']:80;
				$hSock=@fsockopen($aHostInfo['host'],$iPort,
								  $iConnErroNo,$sConnErrStr,$iTimeoutSecs);
				if(!$hSock)
				{
					//fail and thus not cool
					$aInfo['OK']=false;
					$aInfo['message']=sprintf('%s (%d)',trim($sConnErrStr),trim($iConnErroNo));
					return $aInfo;
				}

				if(isset($aHostInfo['path']))
				{
					$sUri.=$aHostInfo['path'];
				}
				if(isset($aHostInfo['query']))
				{
					$sUri.=$aHostInfo['query'];
				}
				$sUri=($sUri)?$sUri:'/';

				$sHead="GET $sUri HTTP/1.1\r\n"
					."Host: {$aHostInfo['host']}\r\n"
					."User-Agent: WaccPing ".WaccSystem\WaccApp::APP_VERSION."\r\n"
					."Connection: Close\r\n"
					."\r\n";

				fwrite($hSock, $sHead);
				$fStart=microtime(true);
				if(($r=fread($hSock,1024))===false)
				{
					return $this->pingHandleSockErr($hSock);
				}
				$fTime=microtime(true)-$fStart;
				$aResp=explode("\n",$r);
				$i=array_search('Status',$aResp);
				$sHttpStatus=trim($aResp[$i]);
				fclose($hSock);
				//var_dump($sHead.$r);
				$aInfo['message']=$sHttpStatus.'; '.strlen($r).' bytes read';
				$aInfo['time']=round($fTime*1000,2); //micro to milli
				break;
			}
			case 'tcp':
			{
				if(!isset($aHostInfo['port'])
				   || $aHostInfo['port']==1)
				{
					//only break if no port or port is 1,
					//we can then assume an ICMP ping...

					//@todo: check for ipv4 vs ipv6

					//thanks to contribs on http://php.net/manual/en/function.socket-create.php
					$sPackage = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
					$hSock  = @socket_create(AF_INET, SOCK_RAW, 1);
					if($hSock===false)
					{
						return $this->pingHandleSockErr();
					}

					socket_set_option($hSock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $iTimeoutSecs, 'usec' => 0));
					if(!@socket_connect($hSock, $aHostInfo['host'], null)) //avoids warnings on unable to connect [10051]
					{
						return $this->pingHandleSockErr($hSock);
					}

					//we got the socket, time to ping...
					$ts = microtime(true);
					if(!socket_send($hSock, $sPackage, strlen($sPackage), 0)
					   || !($r=@socket_read($hSock, 255)))
					{
						return $this->pingHandleSockErr($hSock);
					}
					$result = microtime(true) - $ts;
					socket_close($hSock);
					$aInfo['message']=strlen($r).' bytes read';
					$aInfo['time']=round($result*1000,2); //micro to milli
					break;
				}
			}
			default:
			{
				//just try an fsock open
				$iPort=isset($aHostInfo['port'])?$aHostInfo['port']:-1;
				$sHostFmt='%s://%s:%s';
				$hSock=@fsockopen(sprintf($sHostFmt,$aHostInfo['scheme'],$aHostInfo['host'],$iPort),
								  $iPort,$iConnErroNo,$sConnErrStr,$iTimeoutSecs);
				if(!is_resource($hSock))
				{
					//fail and thus not cool
					return $this->pingHandleSockErr();
				}
				$fStart=microtime(true);
				fwrite($hSock,0xDEC0DED0."\n");
				if(false===($r=fread($hSock,1024)))
				{
					return $this->pingHandleSockErr($hSock);
				}
				$fTime=microtime(true)-$fStart;
				fclose($hSock);
				$aInfo['message']=strlen($r).' bytes read';
				$aInfo['time']=round($fTime*1000,2); //micro to milli
				break;
			}
		}

		//$aInfo=array(
		//	'OK'=>$bStatus,
		//	'message'=>$sMsg,
		//	'time'=>$iTime
		//);
		return $aInfo;
	}

	/**
	 * @param resource handle to falied socket
	 * @return array Ping Info (with fail info)
	 */
	private function pingHandleSockErr($hSocket=false)
	{
		$aInfo=array();
		if(is_resource($hSocket))
		{
			$iSockErrNo=socket_last_error($hSocket);
		}
		else
		{
			$iSockErrNo=socket_last_error();
		}
		$iSockErrNo=(int)$iSockErrNo;
		$sSockErrStr=trim(socket_strerror($iSockErrNo));
		$aInfo['OK']=false;
		$aInfo['message']=sprintf('%s (%d)',$sSockErrStr,$iSockErrNo);
		return $aInfo;
	}
}

class DoPingException extends \RuntimeException
{}
