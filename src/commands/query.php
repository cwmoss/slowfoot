<?php

namespace slowfoot\commands;

use cwmoss\final_cli\cli;
use slowfoot\app;

class query {

    public function __construct(public app $app) {
    }

    /**
     * query your dataset with lolql
     * 
     * executes a lolql query and 
     * prints the json result
     */
    public function __invoke(
        string $__lolql_query,
    ) {
        $this->app->setup()->load_data(true);

        $result = $this->app->project->ds->query($__lolql_query);
        echo json_encode(
            $result,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        ) . PHP_EOL;
    }
}
