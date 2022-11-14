
Game of Life for HTML5
======================

This is a small Game of Life implementation in HTML5, designed specifically
for easy embedding in other pages.

To use, just insert the following in your page::

    <iframe width="W" height="H" style="border: 0;" scrolling="no"
        src="life.html?params"></iframe>

Parameters are specified on the query string (that is, as
``param1=value1&param2=value2``) and need to be escaped as appropriate.  Valid
parameters are:

``width``
    How many cells wide the game should be.  Default: 32.

``height``
    How many cells high the game should be.  Default: 32.

``scale``
    How many pixels each cell should be, on a side.  Default: 4.

``bgColor``
    Background color.  Default: white.

``fgColor``
    Foreground color.  Default: black.

``stepInterval``
    Time between updates, in seconds.  Default: 0.033.

``resetDensity``
    Density of live cells when the game is reset.  Default: 0.2.

``resetInterval``
    How often to reset the game, in seconds.  Set to zero to disable
    periodic resets.  Default: 30.

Note that ``W`` and ``H`` should be ``width`` and ``height`` multiplied by
``scale``.

