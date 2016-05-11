<?php

use Awjudd\AssetProcessor\Asset\LocalAsset;
use Awjudd\AssetProcessor\Asset\RemoteAsset;

class AssetTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_all_javascript_derived_attributes_are_correct()
    {
        $asset = LocalAsset::create('testing/js/foo.js')[0];

        $this->assertTrue($asset->isJavaScript());
        $this->assertFalse($asset->isStyleSheet());
    }

    /**
     * @test
     */
    public function ensure_emited_javascript_html_is_correct()
    {
        $asset = LocalAsset::create('testing/js/foo.js')[0];

        $this->assertEquals(
            sprintf(
                '<script type="text/javascript" src="/assets/testing/js/foo-final-%s.js" ></script>',
                $asset->getModifiedTime()
            ),
            $asset->javascript([])
        );

        $this->assertEquals(
            sprintf(
                '<script type="text/javascript" src="/assets/testing/js/foo-final-%s.js" foo="bar" foobar="&quot;foobar&quot;" ></script>',
                $asset->getModifiedTime()
            ),
            $asset->javascript(['foo' => 'bar', 'foobar' => '"foobar"'])
        );

        $this->assertEquals('', $asset->stylesheet());
        $this->assertEquals('', $asset->stylesheet(['foo' => 'bar', 'foobar' => '"foobar"']));
    }

    /**
     * @test
     */
    public function ensure_all_stylesheet_derived_attributes_are_correct()
    {
        $asset = LocalAsset::create('testing/css/foo.css')[0];

        $this->assertFalse($asset->isJavaScript());
        $this->assertTrue($asset->isStyleSheet());
    }

    /**
     * @test
     */
    public function ensure_emited_stylesheet_html_is_correct()
    {
        $asset = LocalAsset::create('testing/css/foo.css')[0];

        $this->assertEquals(
            sprintf(
                '<link rel="stylesheet" type="text/css" href="/assets/testing/css/foo-final-%s.css"  />',
                $asset->getModifiedTime()
            ), $asset->stylesheet([]));
        $this->assertEquals(
            sprintf(
                '<link rel="stylesheet" type="text/css" href="/assets/testing/css/foo-final-%s.css" foo="bar" foobar="&quot;foobar&quot;"  />',
                $asset->getModifiedTime()
            ),
            $asset->stylesheet(['foo' => 'bar', 'foobar' => '"foobar"'])
        );

        $this->assertEquals('', $asset->javascript());
        $this->assertEquals('', $asset->javascript(['foo' => 'bar', 'foobar' => '"foobar"']));
    }

    /**
     * @test
     */
    public function ensure_folder_derived_attributes_are_correct()
    {
        $assets = LocalAsset::create('testing');

        foreach ($assets as $asset) {
            if ($asset->getName() == 'foo.js') {
                $this->assertTrue($asset->isJavaScript());
                $this->assertFalse($asset->isStyleSheet());

                $this->assertEquals('', $asset->stylesheet([]));
                $this->assertEquals('', $asset->stylesheet(['foo' => 'bar', 'foobar' => '"foobar"']));

                $this->assertEquals(
                    sprintf(
                        '<script type="text/javascript" src="/assets/testing/js/foo-final-%s.js" ></script>',
                        $asset->getModifiedTime()
                    ),
                    $asset->javascript([]));
                $this->assertEquals(
                    sprintf(
                        '<script type="text/javascript" src="/assets/testing/js/foo-final-%s.js" foo="bar" foobar="&quot;foobar&quot;" ></script>',
                        $asset->getModifiedTime()
                    ),
                    $asset->javascript(['foo' => 'bar', 'foobar' => '"foobar"']));
            } else {
                $this->assertFalse($asset->isJavaScript());
                $this->assertTrue($asset->isStyleSheet());

                $this->assertEquals(
                    sprintf(
                        '<link rel="stylesheet" type="text/css" href="/assets/testing/css/foo-final-%s.css"  />',
                        $asset->getModifiedTime()
                    ),
                    $asset->stylesheet([])
                );
                $this->assertEquals(
                    sprintf(
                        '<link rel="stylesheet" type="text/css" href="/assets/testing/css/foo-final-%s.css" foo="bar" foobar="&quot;foobar&quot;"  />',
                        $asset->getModifiedTime()
                    ),
                    $asset->stylesheet(['foo' => 'bar', 'foobar' => '"foobar"'])
                );

                $this->assertEquals('', $asset->javascript([]));
                $this->assertEquals('', $asset->javascript(['foo' => 'bar', 'foobar' => '"foobar"']));
            }
        }
    }

    /**
     * @test
     */
    public function ensure_invalid_file_is_caught()
    {
        $this->expectException(InvalidArgumentException::class);
        $asset = LocalAsset::create('foo.css');
    }

    /**
     * @test
     */
    public function ensure_cdn_asset_metadata()
    {
        $asset = RemoteAsset::create('//doesnt-exist.com/foo.js')[0];

        $this->assertTrue($asset->isJavaScript());
        $this->assertFalse($asset->isStyleSheet());

        $this->assertEquals('<script type="text/javascript" src="//doesnt-exist.com/foo.js" ></script>', $asset->javascript([]));
        $this->assertEquals('<script type="text/javascript" src="//doesnt-exist.com/foo.js" foo="bar" foobar="&quot;foobar&quot;" ></script>', $asset->javascript(['foo' => 'bar', 'foobar' => '"foobar"']));

        $asset = RemoteAsset::create('//doesnt-exist.com/foo.css')[0];

        $this->assertFalse($asset->isJavaScript());
        $this->assertTrue($asset->isStyleSheet());

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="//doesnt-exist.com/foo.css"  />', $asset->stylesheet([]));
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="//doesnt-exist.com/foo.css" foo="bar" foobar="&quot;foobar&quot;"  />', $asset->stylesheet(['foo' => 'bar', 'foobar' => '"foobar"']));
    }

    /**
     * @test
     */
    public function ensure_remote_asset_with_query_string()
    {
        $asset = RemoteAsset::create('https://code.jquery.com/jquery-2.2.3.min.js')[0];

        $this->assertTrue($asset->isJavaScript());
        $this->assertFalse($asset->isStyleSheet());
    }
}
