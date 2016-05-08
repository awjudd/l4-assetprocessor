<?php

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        // Create some dummy files
        mkdir('testing');
        mkdir('testing/js');
        mkdir('testing/css');
        touch('testing/js/foo.js');
        touch('testing/css/foo.css');
    }

    public function tearDown()
    {
        parent::tearDown();

        // Cleanup the environment
        $this->remove('testing');
    }

    /**
     * Used internally in order to clean up everything that we made for testing.
     *
     * @param      string  $path   The path to clean up.
     */
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