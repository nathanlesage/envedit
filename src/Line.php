<?php

namespace NathanLeSage;

class Line
{
    /**
     * These constants are for internal use only
     */
    const LINE_IS_VARIABLE = 1;
    const LINE_IS_COMMENT  = 2;
    const LINE_IS_EMPTY    = 3;

    /**
     * The type of the line. Can be variable, comment or empty
     * @var int
     */
    protected $type;

    /**
     * If this line is a variable, this holds its name
     * @var string
     */
    protected $varname;

    /**
     * This either holds the comment's or the variable's content
     * @var string
     */
    protected $value;

    /**
     * If this line is a variable and has a trailing comment, this var holds it
     * @var string
     */
    protected $trailingComment;

    /**
     * Constructs a new Line object and parses the line.
     * @param string $line The .env-line to be parsed
     */
    public function __construct($line = null)
    {
        if($line === null) {
            throw new Exception('$line is null!');
        }

        if(strlen(trim($line)) == 0) {
            $this->type = LINE_IS_EMPTY;
            return;
        }

        if(strpos(ltrim($line), '#') === 0) {
            $this->type = LINE_IS_COMMENT;
            $this->value = substr(ltrim($line), 1);
            return;
        }

        // Now we got a variable
        $this->type = LINE_IS_VARIABLE;
        $i = 0;

        // Find the first equal sign (so that values may have an equal in them)
        for(; $i < strlen($line); $i++)  {
            if($line[$i] !== '=') {
                $this->varname .= $line[$i];
            } else {
                break;
            }
        }

        // Set pointer to beginning of value
        ++$i;

        if(($i+1) >= strlen($line)) {
            $this->value = '';
        } else {
            $this->value = substr($line, $i);

            // Check for trailing comments
            // Important, a hash WITHOUT preceeding space is NOT a (correct) comment.
            $commentbegin = strpos($this->value, ' #');
            if($commentbegin > 0) {
                $this->trailingComment = ltrim(substr($this->value, $commentbegin + 2));
                $this->value = trim(substr($this->value, 0, $commentbegin));
            }
        }

        // Last step: If we have a string containing whitespaces there are quotes
        // around it -> remove for storing in this object
        $this->value = str_replace('"', '', $this->value);
    }

    /**
     * Returns true, if the line contains a variable
     * @return boolean Whether or not the line is a variable
     */
    public function isVariable()
    {
        return ($this->type === LINE_IS_VARIABLE);
    }

    /**
     * Returns true, if the line contains a comment
     * @return boolean Whether or not the line is a comment
     */
    public function isComment()
    {
        return ($this->type === LINE_IS_COMMENT);
    }

    /**
     * Returns true, if the line is empty
     * @return boolean Whether or not the line is empty
     */
    public function isEmpty()
    {
        return ($this->type === LINE_IS_EMPTY);
    }

    /**
     * Returns true, if the line contains a trailing comment
     * @return boolean Whether or not the line has a trailing comment
     */
    public function hasTrailingComment()
    {
        return (isset($this->trailingComment) && strlen($this->trailingComment) > 0);
    }

    /**
     * Returns the value of the variable, or false
     * @return mixed Either false, or it's value, if variable
     */
    public function getValue()
    {
        if(!$this->isVariable()) {
            return false;
        }

        return $this->value;
    }

    /**
     * Returns the variable name, if the line contains a variable
     * @return mixed Either false, or it's name, if variable
     */
    public function getVarname()
    {
        if(!$this->isVariable()) {
            return false;
        }

        return $this->varname;
    }

    /**
     * If the variable contains a trailing comment, this function returns it
     * @return mixed Either false or the comment, if there is one
     */
    public function getComment()
    {
        if(!$this->hasTrailingComment()) {
            return false;
        }

        return $this->trailingComment;
    }

    /**
     * Changes the value of this line, if it is a variable
     * @param string $newValue A string containing the new variable.
     * @return boolean True on success, false if this line is not a variable
     */
    public function setValue($newValue = null)
    {
        if(!$this->isVariable()) {
            return false;
        }

        if($newValue === null) {
            $this->value = '';
        } else {
            $this->value = $newValue;
        }

        return true;
    }

    /**
     * Prepares the value to be written into a string
     * @return boolean True on success or false if something went wrong
     */
    public function sanitizeValue()
    {
        if(strlen($this->value) == 0) {
            return false;
        }

        if(preg_match('/\s/',$this->value)) {
            // Whitespace-containing fields must be escaped
            $this->value = '"' . $this->value . '"';
        }

        return true;
    }

    /**
     * This function parses the object into a string, that can be written to a file
     * @return string The complete line as a string
     */
    public function getLine()
    {
        if($this->isVariable()) {
            $this->sanitizeValue();

            $ret = $this->getVarname() . '=' . $this->getValue();
            if($this->hasTrailingComment()) {
                $ret .= ' # ' . trim($this->trailingComment);
            }
            return $ret;
        } elseif($this->type == LINE_IS_COMMENT) {
            return '# ' . trim($this->value);
        } else {
            return "";
        }
    }
}
