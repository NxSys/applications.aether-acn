..
	login Docs
	$Id$

.. sectionauthor:: Chris R. Feamster <cfeamster@f2developments.com>
.. codeauthor:: Chris R. Feamster <cfeamster@f2developments.com>
.. index::
   single: login
   pair: System Cmdlets; login
   see: exit; login
   see: logon, logoff; login
.. program:: login

Login
=====

Does what it says on the tin

Syntax
------
::

	login [-m|--method="..."] user cred-string


Purpose
-------

Parameters
----------

Arguments
^^^^^^^^^

``user``
	User name

``cred-string``
	A credential string. By default this will be a password. If you use the :option:`-m` switch a different string may be expected. Please see the particular mode for details.

Options
^^^^^^^

.. option:: -m <mode>, --mode <mode>

	The partiular login mode to use. Such as:
	
	``hash``
	
		Uses a PW hash

	``openid``
	
		Uses openid. Requires setup.




Examples
--------

Login a user with a simple username and password::

	}login flynn b1od1g1talj@zz

Using a pw hash::

	}login -m hash flynn c9438053513b7e38c880d353294ee6c84e4ca2364b2e930555ca76222617ed53

Remarks
-------

Please go read SECURITY.TXT or maybe :ref:`extending`


See Also
--------

SECURITY.TXT