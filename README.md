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

How to install it?
------------------

In order to install your local copy of CheckTimer, you must follow these simple
steps.

### Clone the GitHub repo

Open a terminal and type:

    $ git clone git://github.com/alessandro1997/CheckTimer.git

### Set up your parameters

Copy the file **app/config/parameters.yml.dist** into **app/config/parameters.yml**
and edit the parameters to match your server's configuration.

### Create the database schema

Open a terminal and, in your project's root, type the following commands:

    $ php app/console doctrine:database:create
    $ php app/console doctrine:schema:create

This will create your database schema.

### Set up your levels

Currently there's no administration panel (but it's on the todo list), so you'll
have to enter your levels in the database manually. We recommend using [phpMyAdmin](http://www.phpmyadmin.net/)
for MySQL databases.

### Set up the cleanup cron job

You also must set the following cron job to run every day:

    php app/console checktimer:user:cleanup

It will clean the users table (remove expired registrations, email change
requests and password reset requests).
