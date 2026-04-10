<?php

class abc {
    public static $name = "bineintest";
    function tc() {
        $a = function () {
            print self::$name;
        };
        $a();
    }
}

new abc()->tc();



///

$app->get("/__restart", function () use ($container, &$restart) {
    // $app->
    dbg("+++ restart");
    $container->getRunner()->stop();
    $restart = true;
    // exit;
});

/*
        print $_SERVER["_"] . " -- " . PHP_BINARY . "\n";
        $cmd = [];
        if (PHP_BINARY) {
            $cmd = [PHP_BINARY, $this->app->original_args];
        } else {
            $cmd = [$_SERVER["_"]];
            $args = $this->app->original_args;
            unset($args[0]);
            $cmd[] = $args;
        }
        print_r($cmd);
        */

$this->add_routes($app, $project);
$app->run();
var_dump($restart);
var_dump($this->app->original_args);
// exec("$cmd > /dev/null &");
// $this->execInBackground($this->app->original_args);
if ($restart) {
    // pcntl_exec($cmd[0], $cmd[1]);
    sleep(1);
    pcntl_exec("/opt/homebrew/bin/php", $this->app->original_args);
}

print "finished running\n";
exit;
