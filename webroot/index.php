<?php
// echo sys_get_temp_dir();
/**
 * index.php
 * $Id$
 *
 * DESCRIPTION
 *  Front Controler for WACC
 *
 * @link https://nxsys.org/spaces/wacc
 * @package WACC\System
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 *
 * @copyright Copyright 2013-2015 Nexus Systems, Inc.
 * @license https://nxsys.org/spaces/wacc/wiki/License
 * Please see the license.txt file or the url above for full copyright and
 * license terms.
 */

/**
 * this can be relative or absolute SEE: realpath()
 *
 * if you can't move the index.php file then it should be:
 * 	./approot
 *
 * if you can move it, use the relative OR full path to the application directory:
 *  ../approot
 *   OR
 *  /mnt/virtualized/home/foo/www/wacc/approot
 *   OR EVEN
 *  d:\inetpub\httproot\foo\www_root\wcc\approot
 *
 */
$PATH_TO_APPROOT='../executive';

////////////////////////////////////////////////////////////////////////////////
////////////// PLEASE CHANGE NOTHING ELSE BELOW THIS BARRIER ///////////////////
////////////////////////////////////////////////////////////////////////////////

define('APPROOT_PATH', realpath($PATH_TO_APPROOT));
define('WEBROOT_PATH', realpath(__FILE__));

if(!APPROOT_PATH || !WEBROOT_PATH)
{
	throw new RuntimeException('Please ensure that you have the correct path to '.$PATH_TO_APPROOT.' and that related permissions have been granted.');
}
require_once APPROOT_PATH.'/wacc.php';

$o_Application=\Wacc\System\WaccApp::getInstance();

$o_Application->doRequest();

//all done
