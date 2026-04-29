<?php

namespace slowfoot;

use Closure;

/*

hook::load($custdir."/hooks.php");
account::$order = hook::invoke('account_order', 'accountname');
*/

class hook {

    public static array $h = [];
    public static array $f = [];

    public static function add(string|hooks $name, Closure $fun) {
        if ($name instanceof hooks) $name = $name->name;
        if (!array_key_exists($name, self::$h)) {
            self::$h[$name] = [];
        }

        self::$h[$name][] = $fun;
    }
    public static function add_filter(string|hooks $name, Closure $fun) {
        if ($name instanceof hooks) $name = $name->name;
        if (!array_key_exists($name, self::$f)) {
            self::$f[$name] = [];
        }

        self::$f[$name][] = $fun;
    }
    public static function invoke(string|hooks $action, mixed $default = null, mixed ...$args) {
        if ($action instanceof hooks) $action = $action->name;
        #print_r(self::$h);
        // $args = func_get_args();
        // array_shift($args);
        // array_shift($args);
        # print_r(self::$h);
        if (!array_key_exists($action, self::$h)) {
            return $default;
        }
        $res = [];
        foreach (self::$h[$action] as $meth) {
            // return $meth();
            $res[] = call_user_func_array($meth, $args);
        }
        #var_dump($res);
        return $res;
    }

    public static function invoke_filter(string|hooks $action, mixed $start = null, mixed ...$args) {
        if ($action instanceof hooks) $action = $action->name;
        #print $action;
        #print_r(self::$f);
        #$args = func_get_args();
        #array_shift($args);

        # print_r(self::$h);
        if (!array_key_exists($action, self::$f)) {
            return $start;
        }

        array_unshift($args, $start);
        foreach (self::$f[$action] as $meth) {
            // return $meth();
            $start = call_user_func_array($meth, $args);
        }
        #var_dump($res);
        return $start;
    }
}
