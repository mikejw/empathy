<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin\JSONView;

class ReturnCodes
{
    public const OK = 200;
    public const Created = 201;
    public const No_Content = 204;
    public const Not_Modified = 304;
    public const Bad_Request = 400;
    public const Unauthorized = 401;
    public const Forbidden = 403;
    public const Not_Found = 404;
    public const Method_Not_Allowed = 405;
    public const Conflict = 409;
    public const Unprocessable_Entity = 422;
    public const Internal_Server_Error = 500;

    public static function getName($code)
    {
        $name = '';
        foreach (self::getAll() as $index => $value) {
            if ($value === $code) {
                $name = str_replace('_', ' ', $index);
                break;
            }
        }
        return $name;
    }

    public static function getAll()
    {
        $r = new \ReflectionClass(self::class);
        return $r->getConstants();
    }
}
