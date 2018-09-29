..
	Ping Docs
	$Id$

.. sectionauthor:: Chris R. Feamster <cfeamster@f2developments.com>
.. codeauthor:: Chris R. Feamster <cfeamster@f2developments.com>
.. index::
   single: type
   pair: System Cmdlets; type
.. program:: type

Type
=====

Shows the contents of a file with line numbers

Syntax
------

:: 

 type filename

Purpose
-------
Pings subject for c counts (default 4) with i interval (default 1.0 seconds). There is a count max of 20 and interval must be greater than 0.5 and less than 20 (20 > i > 0.5). 

.. warning:: In many cases WACC and thus :command:`ping` may be on a shared environment with short lived php workers. *Be kind to your environment!* and try not to monopolize processes time.

.. seealso:: For examples of how to use this command, see `Examples`_ below.


Parameters
----------

Arguments
^^^^^^^^^
 `filename`
 	The path of the file you wish to display.

Examples
--------

.. rubric:: Default Operation

Display the specified file::

	}type filename.txt

This would produce output such as the following::

	1. Foo

Remarks
-------

Its Type. And does what it says on the tin.


See Also
--------

* http://code.fnetit.net/projects/wacc/wiki