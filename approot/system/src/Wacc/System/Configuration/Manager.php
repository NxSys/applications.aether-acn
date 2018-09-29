<?php
/*
 *
 *
 *
 *
 *
 */

namespace Wacc\System\Configuartion;

/*
 * class Manager
 *
 * feats:
 *  key-value store
 *  hirearchal KV store
 *  node based KV (as such)
 *  path to KV collection and path to K
 */
class Manager
{
	/*
	 * __construct()
	 * @param $var
	 */
	function __construct($sDBStoreDsn, $aConfigOpts, $sPrefix='')
	{
		/* connect to dsn
		 * check schema ver
		 *  if < updg or create
		 * load\cache baseconf overrides
		 */
	}

	public function newManagerAtPath($sPrefix)
	{
		return new Manager($this->sDBStoreDsn, $this->aConfigops, $sPrefix);
	}

	/*
	 * function get
	 * @param $sNodeName
	 */
	function get($sNodeName)
	{

	}

	/*
	 * function set
	 * @param $nodeName
	 */
	function set($sNodeName, $mValue)
	{

	}



}
