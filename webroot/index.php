<?php
/**
 * index.php
 * $Id$
 *
 * DESCRIPTION
 *  Front Controler for WACC
 *
 * @link https://f2dev.com/prjs/wacc
 * @package WACC
 * @subpackage System
 * @license http://f2dev.com/prjs/wacc/license.html
 * Please see the license.txt file or the url above for full copyright and license information.
 * @copyright Copyright 2013 F2 Developments, Inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
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
$PATH_TO_APPROOT='../approot';

////////////////////////////////////////////////////////////////////////////////
////////////// PLEASE CHANGE NOTHING ELSE BELOW THIS BARRIER ///////////////////
////////////////////////////////////////////////////////////////////////////////

define('APPROOT_PATH',realpath($PATH_TO_APPROOT));
define('WEBROOT_PATH',realpath(__FILE__));

if(!APPROOT_PATH || !WEBROOT_PATH)
{
	throw new RuntimeException('Please ensure that you have the correct path to '.$PATH_TO_APPROOT.' and that related permissions have been granted.');
}
require_once APPROOT_PATH.'/wacc.php';

$o_Application=\Wacc\System\WaccApp::getInstance();

$o_Application->doRequest();

//all done
