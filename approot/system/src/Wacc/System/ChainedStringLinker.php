<?php
/**
 * Makes cmd lines out of a chain
 * $Id$
 *
 * DESCRIPTION
 *  Takes a command string and tokenizes it to an array, then splits the array by a separator
 *
 *  Uses code from Symfony\Component\Console\Input\StringInput
 *
 * @link https://nxsys.org/spaces/wacc
 * @package Wacc\System
 * @license https://nxsys.org/spaces/wacc/wiki/License
 * Please see the license.txt file or the url above for full copyright and
 * license terms.
 * @copyright Copyright 2013-2015 Nexus Systems, Inc.
 *
 * @author Chris R. Feamster <cfeamster@f2developments.com>
 * @author $LastChangedBy$
 *
 * @version $Revision$
 */

//Local Namespace
namespace Wacc\System;

	//Framework Namespaces
use Symfony\Component\Console\Input;


/**
 * class ChainedStringLinker
 */
class ChainedStringLinker //extends StringInput
{
    const REGEX_STRING = '([^ ]+?)(?: |(?<!\\\\)"|(?<!\\\\)\'|$)';
    const REGEX_QUOTED_STRING = '(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')';



	function process($sCombinedString, $sChainLinkChar='|')
	{
		$aSplitString=$this->tokenize($sCombinedString);
		$aCmdChain=array();$i=0;
		foreach($aSplitString as $sItem)
		{
			if($sItem==$sChainLinkChar)
			{
				$i++;
				continue;
			}
			$aCmdChain[$i][]=$sItem;
		}
		return $aCmdChain;
	}


    public function tokenize($input)
    {
        $input = preg_replace('/(\r\n|\r|\n|\t)/', ' ', $input);

        $tokens = array();
        $length = strlen($input);
        $cursor = 0;
        while ($cursor < $length) {
            if (preg_match('/\s+/A', $input, $match, null, $cursor)) {
            } elseif (preg_match('/([^="\' ]+?)(=?)('.self::REGEX_QUOTED_STRING.'+)/A', $input, $match, null, $cursor)) {
                $tokens[] = $match[1].$match[2].stripcslashes(str_replace(array('"\'', '\'"', '\'\'', '""'), '', substr($match[3], 1, strlen($match[3]) - 2)));
            } elseif (preg_match('/'.self::REGEX_QUOTED_STRING.'/A', $input, $match, null, $cursor)) {
                $tokens[] = stripcslashes(substr($match[0], 1, strlen($match[0]) - 2));
            } elseif (preg_match('/'.self::REGEX_STRING.'/A', $input, $match, null, $cursor)) {
                $tokens[] = stripcslashes($match[1]);
            } else {
                // should never happen
                // @codeCoverageIgnoreStart
                throw new \InvalidArgumentException(sprintf('Unable to parse input near "... %s ..."', substr($input, $cursor, 10)));
                // @codeCoverageIgnoreEnd
            }

            $cursor += strlen($match[0]);
        }
        //var_dump($tokens);
        return $tokens;
    }
}