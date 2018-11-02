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


const APP_IDENT = 'Aether';
const APP_NAME = 'Aether.sh: Cloud Command System';
//@todo get branch name or trunk rev
const APP_VERSION = '0.5';

if(!defined('APP_BASE_DIR'))
{
	//because i'm in src, lets walk back a level
	define('APP_BASE_DIR', realpath(__DIR__.'/../'));
}


//**(Phar)Packed Dirs
//App Sources/Classpath
define('APP_SOURCE_DIR',   APP_BASE_DIR.DIRECTORY_SEPARATOR.'src');

//App binary resources (if in phar, consider extracting before using)
define('APP_RESOURCE_DIR', APP_BASE_DIR.DIRECTORY_SEPARATOR.'res');

//3rd Party Classes, Frameworks, and Libraries
define('APP_VENDOR_DIR',   APP_BASE_DIR.DIRECTORY_SEPARATOR.'vendor');


//**(Phar)Unpacked\Redist Dirs
//Shared\Ext Libs e.g. other Phars, PhpExts
define('APP_LIB_DIR', getcwd().DIRECTORY_SEPARATOR.'libs');

//"InSitu" Documentation
define('APP_DOC_DIR', getcwd().DIRECTORY_SEPARATOR.'docs');

//Misc assorted files e.g. config files and etc
define('APP_ETC_DIR', getcwd().DIRECTORY_SEPARATOR.'etc');

//now you can use a bare autoloader, unless we're in a PHAR, then we need a shim...
set_include_path( APP_SOURCE_DIR.PATH_SEPARATOR
				 	.APP_LIB_DIR.PATH_SEPARATOR
				 .APP_VENDOR_DIR.PATH_SEPARATOR
				 .get_include_path());
spl_autoload_register();

//classmaps and include/require bloc's should be here
const APP_AUTOLOADER_FILE = APP_VENDOR_DIR.DIRECTORY_SEPARATOR.'autoload.php';
is_readable(APP_AUTOLOADER_FILE)
	or
	die(sprintf("APP_AUTOLOADER_FILE is not accessable. Unable to proceed. Please check permissions or reinstall.\n\t%s\n",
				APP_AUTOLOADER_FILE));

require_once APP_AUTOLOADER_FILE;