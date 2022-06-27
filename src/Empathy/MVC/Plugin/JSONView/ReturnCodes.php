<?php

namespace Empathy\MVC\Plugin\JSONView;

class ReturnCodes
{
    const OK = 200;
    const Created = 201;
    const No_Content = 204;
    const Not_Modified = 304;
    const Bad_Request = 400;
    const Unauthorized = 401;
    const Forbidden = 403;
    const Not_Found = 404;
    const Conflict = 409;
    const Internal_Server_Error = 500;

    public static function getName($code)
    {
        $name = '';
        foreach (self::getAll() as $index => $value) {
            if ($value == $code) {
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
