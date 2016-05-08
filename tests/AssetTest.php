<?php

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

        $this->assertEquals('<script type="text/javascript" src="" ></script>', $asset->javascript([]));
        $this->assertEquals('<script type="text/javascript" src="" foo="bar" foobar="&quot;foobar&quot;" ></script>', $asset->javascript(['foo' => 'bar', 'foobar' => '"foobar"']));

        $this->assertEquals('', $asset->stylesheet());
        $this->assertEquals('', $asset->stylesheet(['foo' => 'bar', 'foobar' => '"foobar"']));
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

        $this->assertEquals('<link rel="stylesheet" type="text/css" href=""  />', $asset->stylesheet([]));
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="" foo="bar" foobar="&quot;foobar&quot;"  />', $asset->stylesheet(['foo' => 'bar', 'foobar' => '"foobar"']));

        $this->assertEquals('', $asset->javascript());
        $this->assertEquals('', $asset->javascript(['foo' => 'bar', 'foobar' => '"foobar"']));
    }

    /**
     * @test
     */
    public function ensure_folder_derived_attributes_are_correct()
    {
        $asset = new Asset('testing', false);

        $this->assertTrue($asset->isJavaScript());
        $this->assertTrue($asset->isStyleSheet());


    }

    public function ensure_invalid_file_is_caught()
    {
        $this->expectException(InvalidArgumentException::class);
        $asset = new Asset('foo.css', false);
    }
}