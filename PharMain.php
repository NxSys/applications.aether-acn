<?php
//## CRF - This file Copr. 2014 Nexus Systems Incorporated - Licensed as per OSS MIT##

if (class_exists('Phar'))
{
	//get pharname
	define('PHAR_PATH', Phar::running(false));
	define('PHAR_NAME', basename(PHAR_PATH));
	define('APP_BASE_DIR', 'phar://'.PHAR_NAME);
}

//Use non-stupid autoloader
// because phars are a case affected by phpbug #49625
if (!function_exists('_SHIM_MATCH_CLASSFILE_ASIS_LOADER'))
{
	function _SHIM_MATCH_CLASSFILE_ASIS_LOADER($class)
	{
		$aPaths=explode(PATH_SEPARATOR, get_include_path());
		$aExts=array_reverse(explode(',', spl_autoload_extensions()));
		foreach ($aPaths as $sBasePath)
		{
			foreach($aExts as $ext)
			{
				$cpath=$sBasePath.DIRECTORY_SEPARATOR.$class.$ext;
				//echo $cpath."\n";
				if (is_readable($cpath))
				{
					require_once $cpath;
					return true;
				}
			}
		}
		return false;
	}
	spl_autoload_register('_SHIM_MATCH_CLASSFILE_ASIS_LOADER');
}

//this main file does run in the same context as the stub,
//but after ::mapPhar() and ::interceptFileFuncs()
//elsewhere please use APP_BASE_DIR
require_once 'src/Common.php';
if('cli' == PHP_SAPI || 'embed' == PHP_SAPI)
{
	require_once 'ConsoleMain.php';
	//exit(ConsoleMain($argc, $argv))
	return ConsoleMain($argc, $argv);
}
else // 99% sure we're in a web context
{
	require_once 'WebMain.php';
	return WebMain();
}
//and we're done. HLTC gets called shortly after now