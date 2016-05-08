<?php

use Awjudd\AssetProcessor\AssetGroup;

class AssetGroupTest extends TestCase
{
    /**
     * @test
     */
    public function creation_of_cdn_asset_group()
    {
        $group = new AssetGroup('foo', AssetGroup::CDN);
        $this->assertTrue($group->isCdn());
    }

    /**
     * @test
     */
    public function creation_of_internal_asset_group()
    {
        $group = new AssetGroup('foo', AssetGroup::INTERNAL);
        $this->assertFalse($group->isCdn());
    }

    /**
     * @test
     */
    public function creation_of_default_asset_group()
    {
        $group = new AssetGroup('foo');
        $this->assertFalse($group->isCdn());
    }

    /**
     * @test
     */
    public function creation_of_invalid_asset_group()
    {
        $this->expectException(InvalidArgumentException::class);
        $group = new AssetGroup('foo', 'invalid');
    }

    /**
     * @test
     */
    public function adding_of_asset_to_asset_group()
    {
        $group = new AssetGroup('foo');
        $group->add('testing/js/foo.js');

        $this->assertEquals(1, count($group->getAssets()));
    }
}