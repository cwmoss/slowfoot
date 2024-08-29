<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use slowfoot\configuration;

final class ConfigTest extends TestCase {

    public string $output = __DIR__ . "/_output";

    public function testSomeOptions(): void {
        $config = new configuration("testsite");
        $this->assertSame("testsite", $config->site_name);
    }

    public function testStoreOption(): void {
        @mkdir($this->output . "/var");
        $config = configuration::load($this->output, conf: new configuration("testsite"));
        $this->assertFileExists($this->output . "/var/slowfoot.db", "sqlite store was not created");
        $ok = $config->db->add_row(["_id" => "a", "_type" => "t"]);
        $this->assertTrue($ok);

        unlink($this->output . "/var/slowfoot.db");

        $config = configuration::load($this->output, conf: new configuration(store: "memory"));
        $this->assertFileDoesNotExist($this->output . "/var/slowfoot.db", "sqlite file not expected");
        $ok = $config->db->add_row(["_id" => "a", "_type" => "t"]);
        $this->assertTrue($ok);

        $config = configuration::load($this->output, conf: new configuration(store: "sqlite:memory"));
        $this->assertFileDoesNotExist($this->output . "/var/slowfoot.db", "sqlite file not expected");
        $ok = $config->db->add_row(["_id" => "a", "_type" => "t"]);
        $this->assertTrue($ok);
    }
}
