<?php
//place loader and bootstrap code here

define('APP_IDENT', 'AESH');
define('APP_NAME',  'AetherShell: WebApp Command Console');
define('APP_VERSION', '1.0');

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
define('APP_LIB_DIR', APP_BASE_DIR.DIRECTORY_SEPARATOR.'libs');

//"InSitu" Documentation
define('APP_DOC_DIR', APP_BASE_DIR.DIRECTORY_SEPARATOR.'docs');

//Misc assorted files e.g. config files and etc
define('APP_ETC_DIR', APP_BASE_DIR.DIRECTORY_SEPARATOR.'etc');

//classmaps and include/require bloc's should be here

//define('APP_REQ_EXTS_SEP','|');
//define('APP_REQ_EXTS', '');

set_include_path( APP_SOURCE_DIR.PATH_SEPARATOR
				 .APP_VENDOR_DIR.PATH_SEPARATOR
				 .get_include_path());
spl_autoload_register();