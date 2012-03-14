CheckTimer
==========

CheckTimer is a Web game using the [Symfony2 Web framework](http://www.symfony.com).
The game is dead-simple: start the timer, and stop it between the given bounds.

In the [online version](http://www.checktimer.it), I have set up a working
version with ten levels.

...and the nerdy part?
----------------------

Well, CheckTimer is a simple Symfony2 Web application which relies on sessions
to memorize the user's state through the game, so that he can't alterate the
data.

It was based on [Silex](http://www.silex-project.org) before, but I decided to
migrate to Symfony to have more flexibility and a better organized project.

It currently uses Symfony 2.0.11.
