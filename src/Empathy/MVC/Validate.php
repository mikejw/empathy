<?php
/**
 * Empathy model validation
 * @file            Empathy/Validate.php
 * @description     Basic validation of Empathy models
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
namespace Empathy\MVC;

class Validate
{
    const TEXT =  1;
    const ALNUM = 2;
    const NUM = 3;
    const EMAIL = 4;
    const TEL = 5;
    const USERNAME = 6;
    const URL = 7;

    public $error = array();
    private $email_pattern;
    private $allowed_pattern_1;
    private $unix_username_pattern;
    private $twitter_style_username;

    /**
     * Creates validation object
     */
    public function __construct()
    {
        $this->email_pattern = '/^[^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';
        $this->allowed_pattern_1 = '/["\/\-\s:,\']/';
        $this->unix_username_pattern = '/^[a-z][_a-zA-Z0-9-]{3,7}$/';
        $this->twitter_style_username = '/^\w{1,15}$/';

        // taken from http://bit.ly/AQFAn
        $this->url_pattern = '|https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?|';
    }

    /**
     * Perform validation of data based on type spcified
     *
     * @return boolean $valid
     */
    public function valType($type, $field, $data, $optional, $message = null)
    {
        $valid = true;
        if ($data != '') {
            switch ($type) {
                case self::TEXT:
                    if (!ctype_alnum(preg_replace($this->allowed_pattern_1, '', $data))) {
                        $valid = false;
                    }
                    break;
                case self::ALNUM:
                    if (!ctype_alnum($data)) {
                        $valid = false;
                    }
                    break;
                case self::NUM:
                    if (!is_numeric($data)) { // consider ctype_digit instead?
                        $valid = false;
                    }
                    break;
                case self::EMAIL:
                    if (!preg_match($this->email_pattern, $data)) {
                        $valid = false;
                    }
                    break;
                case self::TEL:
                    if (!ctype_digit(preg_replace('/\s/', '', $data))) {
                        $valid = false;
                    }
                    break;
                case self::USERNAME:
                    //if(!preg_match($this->unix_username_pattern, $data))
                    if (!preg_match($this->twitter_style_username, $data)) {
                        $valid = false;
                    }
                    break;
                case self::URL:
                    if (!preg_match($this->url_pattern, $data)) {
                        $valid = false;
                    }
                    break;
                default:
                    die('No valid validation type specified.');
                    break;
            }

            if (!$valid) {
                if (is_string($message)) {
                    $this->addError($message, $field);
                } elseif (isset($message[0])) {
                    $this->addError($message[0], $field);
                } else {
                    $this->addError('Invalid '.$field, $field);
                }
            }
        } elseif (!$optional && $data == '') {
            if (is_array($message) && isset($message[1])) {
                $this->addError($message[1], $field);
            } else {
                $this->addError('This is a required field', $field);
            }
        }

        return $valid;
    }

    /**
     * Associate error message with
     * data structure of object used for storing results of validation
     * using name of field as index.
     *
     * @param  string $message error message
     * @param  string $field   the field to apply the error message to
     * @return void
     */
    public function addError($message, $field)
    {
        if ($field != '') {
            if (isset($this->error['field'])) {
                die('Attempt to overwrite error field.');
            } else {
                $this->error[$field] = $message;
            }
        } else {
            array_push($this->error, $message);
        }
    }

    /**
     * Returns whether the validation object has
     * recorded errors.
     * @return boolean $errors whether the error data structure is empty or not.
     */
    public function hasErrors()
    {
        return (sizeof($this->error) > 0);
    }

    /**
     * Returns the error data structure
     * @return array $error the error data structure belonging to the validation object
     */
    public function getErrors()
    {
        return $this->error;
    }
}
