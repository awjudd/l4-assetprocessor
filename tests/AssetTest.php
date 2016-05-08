<?php

use InvalidArgumentException;
use Awjudd\AssetProcessor\Asset;

class AssetTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_all_javascript_derived_attributes_are_correct()
    {
        $asset = new Asset('testing/js/foo.js', false);

        $this->assertTrue($asset->isJavaScript());
        $this->assertFalse($asset->isStyleSheet());
    }

    /**
     * @test
     */
    public function ensure_emited_javascript_html_is_correct()
    {
        $asset = new Asset('testing/js/foo.js', false);
        $this->assertEquals('<script type="text/javascript" src="" ></script>', $asset->get([]));
        $this->assertEquals('<script type="text/javascript" src="" foo="bar" foobar="&quot;foobar&quot;" ></script>', $asset->get(['foo' => 'bar', 'foobar' => '"foobar"']));
    }

    /**
     * @test
     */
    public function ensure_all_stylesheet_derived_attributes_are_correct()
    {
        $asset = new Asset('testing/css/foo.css', false);

        $this->assertFalse($asset->isJavaScript());
        $this->assertTrue($asset->isStyleSheet());
    }

    /**
     * @test
     */
    public function ensure_emited_stylesheet_html_is_correct()
    {
        $asset = new Asset('testing/css/foo.css', false);
        $this->assertEquals('<link rel="stylesheet" type="text/css" href=""  />', $asset->get([]));
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="" foo="bar" foobar="&quot;foobar&quot;"  />', $asset->get(['foo' => 'bar', 'foobar' => '"foobar"']));
    }

    public function ensure_invalid_file_is_caught()
    {
        $this->expectException(InvalidArgumentException::class);
        $asset = new Asset('foo.css', false);
    }
}