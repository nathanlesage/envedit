# EnvEdit

A simple class for editing the .env-file in your project via PHP.

## Why edit your .env-file via PHP?

Normally, the .env-file is used by developers to adapt to the current
environment like production, develop, local, etc. To adapt to these
environments, it is necessary to store different credentials (like
database username and password, etc.) for different environments.

But if you are developing an app that relies on an .env-file and should
be distributed to end users, you cannot assume they know which values for
an .env-file are correct and which are not. Also, you might not want your
end users to always ssh to your server to edit the file, but do it with
a comfortable GUI. So this library enables you to easily read an
.env-file, edit the values and re-write the file with the new variables.

## Installation

You can install it easily via composer:

```shell
$ composer require nathanlesage/envedit
```

## Usage

You only need to interact with the `EnvEdit` class. You may access your
.env-file by passing its path to a new `EnvEdit` object:

```php
use NathanLeSage\EnvEdit;

...

$editor = new EnvEdit('/path/to/your/.env');

// Read and parse the file

$editor->read();

// Retrieve the value of a specific variable.
// Of course, you should normally do this via env('APP_ENVIRONMENT')
// or similar.

$value = $editor->getValue('APP_ENVIRONMENT');

// Change one or more variables

$editor->setVars(
    ['APP_ENVIRONMENT' => 'production',
    'REDIS_HOST' => '123.321.12.32']
    );

// Write your changes to file

$editor->write();

// You could also retrieve the file's contents without writing them.
// This is useful for backup purposes or letting the user control what
// will be altered.

$editor->getFile();
```

## Contribution

If you would like to contribute, just fork this library and send a pull
request with your changes! I appreciate every help!

## License

This library is licensed under the terms of the [MIT license](http://opensource.org/licenses/MIT).
