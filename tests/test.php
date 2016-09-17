<?php

// This file tests the functionality of both the Line-class as well as the EnvEdit-class
//
// Run it by typing `php tests/test.php` in command line.


require_once __DIR__ . '/../vendor/autoload.php';

use NathanLeSage\EnvEdit;
use NathanLeSage\Line;

// First copy over the test-environment
file_put_contents(__DIR__ .'/files/.env', file_get_contents(__DIR__.'/files/.env.test'));


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*                  THESE TESTS ASSERT THE LINE OBJECT                       *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
echo "=====================\n\n";
echo "  TESTING LINE-CLASS \n\n";
echo "=====================\n\n";

$line = new Line('APP_TITLE="This is an awesome title!" # Trailing comment');

if($line->isVariable()) {
    echo "The Line is a variable!\n";
} else {
    echo "ERROR! The line has been parsed wrongly!\n";
}

if($line->getVarname() == "APP_TITLE") {
    echo "The line has the correct VarName of APP_TITLE!\n";
} else {
    echo "ERROR! The line has not the correct varname!\n";
}

if($line->getValue() == "This is an awesome title!") {
    echo "The line has the correct value!\n";
} else {
    echo "ERROR! The line has a wrong value of " . $line->getValue() . "!\n";
}

echo "Our line has " . (($line->hasTrailingComment()) ? "a " : "no ") . "trailing comment. It is the following: " . $line->getComment() . "\n";

if($line->setValue("Completely different title.")) {
    echo "The value of our line has been changed correctly.\n";
} else {
    echo "There was an error when changing the value of our line!\n";
}

echo "The new line of ours is the following:\n\n" . $line->getValue() . "\n\n";

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*                THESE TESTS ASSERT THE ENVEDIT OBJECT                      *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
echo "\n\n=====================\n\n";
echo "TESTING ENVEDIT-CLASS\n\n";
echo "=====================\n\n";


$editor = new EnvEdit(__DIR__ . '/files/.env');

echo "Reading file...\n";
$editor->read();

echo "Changing variables APP_TITLE, REDIS_HOST and MAIL_PASSWORD...\n";
// Change some values
$editor->setVars(['APP_TITLE' => 'This is a completely new title',
'REDIS_HOST' => '200.100.200.100',
'MAIL_PASSWORD' => 'superSecretPassword']);

echo "Files read and written. The value of APP_TITLE is: ";
echo $editor->getValue("APP_TITLE") . "\n";

echo "Writing changes ...\n";
$editor->write();

echo "Re-Reading the file ...\n";
// Now check if the new settings are set (Re-read the file)
$editor->read();

echo "Outputting the file contents...\n\n\n";
echo "=====================================\n\n";

echo $editor->getFile();

echo "\n\n";
