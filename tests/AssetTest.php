<?php

use Awjudd\AssetProcessor\Asset;

class AssetTest extends TestCase
{
    /**
     * @test
     */
    public function testDerivedAttributes()
    {
        $asset = new Asset('foo.js', false);

        $this->assertTrue($asset->isJavaScript());
        $this->assertFalse($asset->isStyleSheet());

        $this->assertEquals('js', $asset->getExtension());
        $this->assertEquals('<script type="text/javascript" src="" ></script>', $asset->get([]));
    }
}