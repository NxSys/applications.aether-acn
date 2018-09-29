..
    Ping Docs
    $Id$

.. sectionauthor:: Chris R. Feamster <cfeamster@f2developments.com>
.. sectionauthor:: $LastChangedBy$
.. codeauthor:: Chris R. Feamster <cfeamster@f2developments.com>
.. index::
    single: motd
    pair: System Cmdlets; motd
    see: login banner; motd
.. program:: motd

Motd
====

Displays text banners typically before and after login

Syntax
------
:: 

 motd [type]

Purpose
-------
Displays 

.. seealso:: For examples of how to use this command, see `Examples`_ below.


Parameters
----------

Arguments
^^^^^^^^^
`type`
    A message of one of the following types:

    `prelogin`
        Message displayed when the console is first loaded. This is the default.

        This message my be set with the :confval:`site.banner` config value.

    `post`
        Message displayed once a user has logged in to the console.

        This message may be set with the :confval:`site.banner.post` config value.



Examples
--------

.. rubric:: Default Operation

Simply display the prelogin banner::

    }motd


.. tip:: This is the same as typing :program:`motd prelogin`

.. rubric:: Nominal Operation

fooo

Remarks
-------

Its motd. And does what it says on the tin.

You may notice that this cmdlet is one of only two (the other one is `cmdlets\\login`_ ) that can be run *without* logging in. This is because :program:`motd` actually has a property flag on it allowing it to be executed without an authenticated user.

.. code-block:: php
   :emphasize-lines: 9
   
   <?php
   //snip

    class motdCmdlet extends WaccSystem\Cmdlet
    {
        /**
         * @var bool Tells wacc that this cmdlet can be run by anonymous users
         */
        const ALLOW_ANON=true;

        public function configure()
        {

    //snip

Clearly without this flag the MoTD banner could not be shown as it is displayed *before* the prompt is.

.. seealso:: See `extending`_ for more information on creating cmdlets and `security`_ for the implications of anonymous access

See Also
--------

* http://code.fnetit.net/projects/wacc/wiki