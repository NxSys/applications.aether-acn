..
	Phpinfo Docs
	$Id$

.. sectionauthor:: Chris R. Feamster <cfeamster@f2developments.com>
.. codeauthor:: Chris R. Feamster <cfeamster@f2developments.com>
.. index::
   single: phpinfo
   pair: System Cmdlets; phpinfo
.. program:: phpinfo

Phpinfo
========

Outputs information about PHP's configuration

Syntax
------

:: 

 phpinfo [-s|--select="..."] [--break] [what]

Purpose
-------
Outputs information about PHP’s configuration as a structured array.

.. seealso:: For examples of how to use this command, see **Examples** below.


Parameters
----------

Arguments
^^^^^^^^^

``what``

	Selects a particular config section such as INFO_GENERAL, INFO_CREDITS, INFO_CONFIGURATION, INFO_MODULES, INFO_ENVIRONMENT, INFO_VARIABLES, INFO_LICENSE, or INFO_ALL (the default). 
	.. see: The php manual, http://php.net/manual/en/function.phpinfo.php for more information.

Options
^^^^^^^

.. option:: -s, --select="..."

	Selects one particular top level section such as ``General`` or ``mysql`` or ``hash``.

.. option:: --break

	Output the "normal" phpinfo html. May break page’s CSS


Examples
--------

Show *all* available information::

	}phpinfo

Which will produce output similar to::

	Array
	(
	    [General] => Array
	        (
	            [System] => Windows NT COMPUCOMP 6.0 build 6002 (Windows Vista Ultimate Edition Service Pack 2) i586
	            [Build Date] => Mar  9 2011 15:46:03
	            [Compiler] => MSVC9 (Visual C++ 2008)
	            [Architecture] => x86
	            [Server API] => CGI/FastCGI
	            [Virtual Directory Support] => disabled
	            [Configuration File (php.ini) Path] => C:\Windows
	[...]

Show information about SQLite::

	}phpinfo -sSQLite

Thus::

	Array
	(
	    [PECL Module version] => 2.0-dev $Id$
	    [SQLite Library] => 2.8.17
	    [SQLite Encoding] => iso8859
	    [sqlite.assoc_case] => Array
	        (
	            [local] => 0
	            [master] => 0
	        )

	)

Exit Values
-----------

If the command fails it will return -1;

Remarks
-------
.. tip::
	try:
		:option:`-s`General for 

See Also
--------

* http://code.fnetit.net/projects/wacc/wiki