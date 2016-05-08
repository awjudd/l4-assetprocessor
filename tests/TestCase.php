<?php

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Create some dummy files
        mkdir('testing');
        touch('testing/foo.js');
        touch('testing/foo.css');
    }

    public function tearDown()
    {
        // Cleanup the environment
        $this->remove('testing');
    }

    private function remove($path)
    {
        $files = glob($path . '/*');

        foreach ($files as $file) {
            is_dir($file) ? $this->remove($file) : unlink($file);
        }

        rmdir($path);

        return;
    }

}