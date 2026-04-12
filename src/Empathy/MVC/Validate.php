<?php

declare(strict_types=1);
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
    public const TEXT =  1;
    public const ALNUM = 2;
    public const NUM = 3;
    public const EMAIL = 4;
    public const TEL = 5;
    public const USERNAME = 6;
    public const URL = 7;
    public const PASSWORD = 8;
    public const NAME = 9;

    /** @var array<int|string, string> */
    private array $error = [];
    private string $email_pattern = '/^[^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';
    private string $allowed_pattern_1 = '/["\\/\\-\\s:,\\\']/';
    private string $unix_username_pattern = '/^[a-z][_a-zA-Z0-9-]{3,7}$/';
    private string $twitter_style_username = '/^[_a-zA-Z0-9]{1,15}$/';
    private string $allowed_pw_pattern = "/[\"\-\s:,\'\+&\|!\(\)\{\}\[\]\^~\*\?;@£\$]/";
    // taken from https://stackoverflow.com/a/3809435
    private string $url_pattern = '|https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)|';
    private string $name_pattern = "/^(?=.*\p{L})[\p{L} '’\-]+$/u";
    /**
     * Perform validation of data based on type spcified
     *
     * @return boolean $valid
     */
    /**
     * @param array{0?: string, 1?: string}|string|null $message
     */
    public function valType(int $type, string $field, string $data, bool $optional, string|array|null $message = null): bool
    {
        $valid = true;
        if ($data !== '') {
            switch ($type) {
                case self::TEXT:
                    $filtered = preg_replace($this->allowed_pattern_1, '', $data);
                    $filtered = preg_replace($this->allowed_pw_pattern, '', $data);
                    if (!ctype_alnum((string) $filtered)) {
                        $valid = false;
                    }
                    break;
                case self::PASSWORD:
                    $matches = [];
                    $pattern = sprintf(
                        '/^(?=.*[%s])(?=.*[A-Z])(?=.*[a-z])(?=.*\d).*$/',
                        substr($this->allowed_pw_pattern, 2, -2)
                    );
                    preg_match($pattern, $data, $matches, PREG_OFFSET_CAPTURE);
                    if (count($matches) === 0) {
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
                    if (!ctype_digit((string) preg_replace('/\s/', '', $data))) {
                        $valid = false;
                    }
                    break;
                case self::USERNAME:
                    if (!preg_match($this->twitter_style_username, $data)
                        && !preg_match($this->unix_username_pattern, $data)) {
                        $valid = false;
                    }
                    break;
                case self::URL:
                    if (!preg_match($this->url_pattern, $data)) {
                        $valid = false;
                    }
                    break;

                case self::NAME:
                    if (!preg_match($this->name_pattern, $data)) {
                        $valid = false;
                    }
                    break;
                default:
                    throw new \Exception('No valid validation type specified.');
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
        } elseif (!$optional) {
            if (is_array($message) && isset($message[1])) {
                $this->addError($message[1], $field);
            } else {
                $this->addError('This is a required field', $field);
            }
            $valid = false;
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
     */
    public function addError(string $message, string $field): void
    {
        if ($field !== '') {
            if (isset($this->error['field'])) {
                throw new \Exception('Attempted to overwrite error field value');
            } else {
                $this->error[$field] = $message;
            }
        } else {
            $this->error[] = $message;
        }
    }

    /**
     * Returns whether the validation object has
     * recorded errors.
     * @return boolean $errors whether the error data structure is empty or not.
     */
    public function hasErrors(): bool
    {
        return (count($this->error) > 0);
    }

    /**
     * @return array<int|string, string>
     */
    public function getErrors(): array
    {
        return $this->error;
    }
}
