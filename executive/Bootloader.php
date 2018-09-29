<?php
/**
 * $BaseName$
 * $Id$
 *
 * DESCRIPTION
 *  A Core file for Aether.sh
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

///global namespace///
use NxSys\Frameworks\Aether;

use NxSys\Core\ExtensibleSystemClasses as CoreEsc;

use Symfony\Component\DependencyInjection as SfDI;
use Symfony\Component\Config as sfConfig;

use \InvalidArgumentException as InvalidArgumentException;

//our working dir is ./executive
chdir(dirname(__FILE__)); //jump from out of the webroot

//start
#@todo still gonna have issues with phar files.....
require_once '../src/Common.php';

class InitializationException extends CoreEsc\SPL\LogicException {}

class BootLoader
{
	const CONTAINER_FILE='c3.xml';

	static $oInstance;
	public $oDIContainer;

	static function boot()
	{
		if (isset(self::$oInstance))
		{
			throw new InitializationException('can only boot once');
		}
		self::$oInstance=new BootLoader;

		//exec self check tests
	}

	private function __construct()
	{
		$this->initContainers();
	}

	public function initContainers()
	{
		$container = new SfDI\ContainerBuilder();

		$search_paths=[getcwd().DIRECTORY_SEPARATOR.'config',
					   getcwd().DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'services'];

		//first ini files
		$ini_loader = new SfDI\Loader\IniFileLoader($container, new sfConfig\FileLocator($search_paths));
		$ini_loader->load('config.sample.ini');

		//now xml files
		$loader = new SfDI\Loader\XmlFileLoader($container, new sfConfig\FileLocator($search_paths));
		$loader->load(self::CONTAINER_FILE);
		$this->oDIContainer=$container;
	}

	/**
	 * function getDependency
	 * @param object
	 */
	static function getDependency($sSvcName)
	{
		return self::$oInstance->oDIContainer->get($sSvcName);
	}

	/**
	 *
	 *  @throws \InvalidArgumentException
	 */
	static function getConfigParamIfExists($sParam)
	{
		if(self::$oInstance->oDIContainer->hasParameter($sParam))
		{
			return self::getConfigParam($sParam);
		}
		throw new InvalidArgumentException($sParam.' is not defined');
	}

	static function getConfigParam($sParam)
	{
		if(self::$oInstance->oDIContainer->hasParameter($sParam))
		{
			return self::$oInstance->oDIContainer->getParameter($sParam);
		}
		return false;
	}




}
Bootloader::boot();

//pre parse cmdlet files, perhaps compile them all to a cache file

//int dsvfs?

////////////////////////////////////////////////////////////////////////////////
///////////////////////////  FOR THE AESH APP  ///////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
