..
	Ping Docs
	$Id$

.. sectionauthor:: Chris R. Feamster <cfeamster@f2developments.com>
.. codeauthor:: Chris R. Feamster <cfeamster@f2developments.com>
.. index::
	single: ping
	pair: System Cmdlets; ping
.. program:: ping

Ping
=====

Does what it says on the tin.

Syntax
------

::

 ping [-c|--count="..."] [-i|--interval="..."] [subject]

Purpose
-------
Pings subject for c counts (default 4) with i interval (default 1.0 seconds). There is a count max of 20 and interval must be greater than 0.5 and less than 20 (20 > i > 0.5).

.. warning:: In many cases WACC and thus :command:`ping` may be on a shared environment with short lived php workers. *Be kind to your environment!* and try not to monopolize processes time.

.. seealso:: For examples of how to use this command, see `Examples`_ below.


Parameters
----------

Arguments
^^^^^^^^^
 subject protocol:// is a php stream wrapper. Must be in the format of http://HOST[[:PORT]/[URL]] or tcp://HOST[:PORT]

Options
^^^^^^^

.. option:: -c, --count="..."

	Number of times to ping.

.. option:: -i, --interval="..."

	Time interval between pings in seconds.


Examples
--------

.. rubric:: Default Operation

Simply make a http ping on local host::

	}ping

This would produce output such as the following::

	Pinging compucomp: [10.1.1.120]...
	Status from http://compucomp: HTTP/1.1 200 OK; 931 bytes read time=41.1ms
	Status from http://compucomp: HTTP/1.1 200 OK; 931 bytes read time=0.95ms
	Status from http://compucomp: HTTP/1.1 200 OK; 931 bytes read time=0.71ms
	Status from http://compucomp: HTTP/1.1 200 OK; 931 bytes read time=0.69ms

	Ping statistics for http://compucomp
	    Packets: Sent = 4, Received = 4, Lost = 0 (0% loss),
	Approximate round trip times in milli-seconds:
	    Minimum = 0.69ms, Maximum = 41.1ms, Average = 5.77875ms

.. tip:: This is the same as typing :command:`ping me`

.. rubric:: Nominal Operation

Pinging remote host with tcp::

	}ping tcp://ping.yahoo.com

This would produce output similar to the following::

	Pinging ping.yahoo.com: [67.195.182.28]...
	Status from tcp://ping.yahoo.com: 36 bytes read time=23.6ms
	Status from tcp://ping.yahoo.com: 36 bytes read time=23.42ms
	Status from tcp://ping.yahoo.com: 36 bytes read time=18.45ms
	Status from tcp://ping.yahoo.com: 36 bytes read time=18.49ms

	Ping statistics for tcp://ping.yahoo.com
	    Packets: Sent = 4, Received = 4, Lost = 0 (0% loss),
	Approximate round trip times in milli-seconds:
	    Minimum = 18.45ms, Maximum = 23.6ms, Average = 19.735ms

Here we *http* ping on port 81 to a service that **does not** respond with status 200::

	}ping http://1and1.feamsternet.net:81

This would produce output similar to the following::

	Pinging 1and1.feamsternet.net: [74.208.181.156]...
	Status from http://1and1.feamsternet.net:81: HTTP/1.1 401 Authorization Required; 761 bytes read time=59.83ms
	Status from http://1and1.feamsternet.net:81: HTTP/1.1 401 Authorization Required; 761 bytes read time=51.87ms
	Status from http://1and1.feamsternet.net:81: HTTP/1.1 401 Authorization Required; 761 bytes read time=53.83ms
	Status from http://1and1.feamsternet.net:81: HTTP/1.1 401 Authorization Required; 761 bytes read time=52.9ms

	Ping statistics for http://1and1.feamsternet.net:81
	    Packets: Sent = 4, Received = 4, Lost = 0 (0% loss),
	Approximate round trip times in milli-seconds:
	    Minimum = 51.87ms, Maximum = 59.83ms, Average = 53.87ms

And finally we **tcp** ping the same service on the same port::

	}ping tcp://1and1.feamsternet.net:81

This would produce output similar to the following::

	Pinging 1and1.feamsternet.net: [74.208.181.156]...
	Status from tcp://1and1.feamsternet.net:81: 525 bytes read time=51.94ms
	Status from tcp://1and1.feamsternet.net:81: 525 bytes read time=50.94ms
	Status from tcp://1and1.feamsternet.net:81: 525 bytes read time=51.99ms
	Status from tcp://1and1.feamsternet.net:81: 525 bytes read time=52.01ms

	Ping statistics for tcp://1and1.feamsternet.net:81
	    Packets: Sent = 4, Received = 4, Lost = 0 (0% loss),
	Approximate round trip times in milli-seconds:
	    Minimum = 50.94ms, Maximum = 52.01ms, Average = 51.8625ms

Remarks
-------

Its ping. And does what it says on the tin.

.. note:: If you want to do a ssl ping use ping ssl://hostname

.. note::
	Regarding ``tcp://<host>`` pings:

	`tcp://` pings *without* a port perform *ICMP* pings. Unlike most shell accounts, many webserver user accounts are minimaly privledged (rightfully so). However one of the common privledges missing is the ability to open *raw sockets*. We need this privelegde to use SOCK_RAW, as it is the only way to send ICMP traffic properly. Without this privelege, ICMP pings may fail with a message similar to ``Operation not permitted``. Unless your webserver authorizes you with some type of passthrough authentication, there is no recomened way to deal with this, since generally only root\\Administrator can open raw sockets.

	However `tcp://` pings perfomed with a port, ``tcp://host:21``, are pure TCP and should run with no problem. However, the possiblity remains that your host might block socket access altogether. Thus forcing you to resort to protocal pings only. E.g. ``http://ping.yahoo.com``

See Also
--------

* http://code.fnetit.net/projects/wacc/wiki