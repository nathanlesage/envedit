<?php

namespace NathanLeSage;

use NathanLeSage\Line;

class EnvEdit
{
    /**
     * The path to the .env-file
     * @var string
     */
    protected $file = '';

    /**
     * An array of lines contained in the file
     * @var array
     */
    protected $lines = [];

    /**
     * Constructor checks the file's existance and determines the .env-file if possible
     * @param string $file The full path to the file
     */
    public function __construct($file = null)
    {
        if(isset($file)) {
            $this->file = $file;
        } elseif(function_exists('base_path')) { // We got a laravel installation
            $this->file = base_path() . '/.env';
        } else {
            // We have no path, so determine it based on composer assumptions:
            // root/vendor/nathanlesage/envedit/src/__FILE__
            $this->file = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/.env';
        }

        if(!is_file($this->file)) {
            throw new Exception("Could not find .env-file!");
        }
    }

    /**
     * Reads the contents of the file into its line-array
     * @return EnvEdit Returns $this for chainability
     */
    public function read()
    {
        if(count($this->lines) > 0) { // We are currently re-reading a file
            $this->lines = [];
        }

        $oldEnv = file_get_contents($this->file);

        if(false === $oldEnv) {
            throw new Exception("Could not open .env-file!");
        }

        // Arrayfy the file
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $oldEnv) as $line) {
            $this->lines[] = new Line($line);
        }

        // For chainability
        return $this;
    }

    /**
     * This sets new values for any variables in the file. Important: It does NOT add new ones!
     * @param array $newValues An associative array containing varnames and their new values
     * @return EnvEdit Returns $this for chainability
     */
    public function setVars(array $newValues)
    {
        // Now write all fields with the new values
        foreach($newValues as $name => $field) {

            foreach($this->lines as $line) {
                if(!$line->isVariable()) {
                    continue;
                }

                if($line->getVarname() == $name) {
                    $line->setValue($field);
                }
            }
        }

        return $this;
    }

    /**
     * Returns the value of a specific .env-variable
     * @param  string $varname The variable, for which the value is desired
     * @return mixed          Either false, if the variable does not exist or its value
     */
    public function getValue($varname)
    {
        foreach($this->lines as $line) {
            if(!$line->isVariable()) {
                continue;
            }

            if($line->getVarname() == $varname) {
                return $line->getValue();
            }
        }

        return false;
    }

    /**
     * This function returns the file instead of writing it. Useful for backing up the .env-file
     * @return string The complete file contents
     */
    public function getFile()
    {
        $newEnv = '';
        // Rebuild the file
        foreach($this->lines as $line) {
            $newEnv .= $line->getLine() . "\n";
        }

        return $newEnv;
    }

    /**
     * Overwrites the .env-file with the current object buffer
     * @return boolean Returns true on successful write, or false otherwise
     */
    public function write()
    {
        $newEnv = '';
        // Rebuild the file
        foreach($this->lines as $line) {
            $newEnv .= $line->getLine() . "\n";
        }

        // And write it!
        return (file_put_contents($this->file, rtrim($newEnv)) !== false);
    }
}
