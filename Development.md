# Checkout from Mercurial #

Instructions are found [here](http://code.google.com/p/narro/source/checkout).

# Data directory #

Create a directory that will hold Narro's data. If you can put it where it can't be accessed through the web.

The directory needs to be writable by the web user. That is chmod 777 on it.

Edit the [configuration/configuration.narro.inc.php](http://code.google.com/p/narro/source/browse/narro/configuration/configuration.narro.inc.php) file.

The default configuration expects Narro to be installed in a subdirectory narro/narro of your web root and it would be accessible under http://localhost/narro/narro .

It also expects a running local mysql server, the database narro accessible by the user narro, no password.

Change these to fit your needs.

# Database #

Create a database narro and import narro.sql in it.

# First run #

That should be it. If you have any problems, please use the [issue tracker](http://code.google.com/p/narro/issues/entry) and I'll do my best to help you.

Register. The first registered user automatically gets Administrator permissions to manage everything.