<?php

namespace App\Utility;
/*
 * 工具类
 * */

class SysTools
{
    public static function generateToken($user_id)
    {
        return md5("asd123))-+" . $user_id . "__1A");
    }

    public static function authToken($user_id, $token)
    {
        return self::generateToken($user_id) == $token;
    }
}