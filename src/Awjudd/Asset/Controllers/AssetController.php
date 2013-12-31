<?php namespace Awjudd\Asset\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    public function getIndex($type, $name)
    {
        $available = \Config::get('asset::controller.available', array());

        // Validate the type
        if(in_array($type, $available))
        {
            // Verify that the name is an MD5
            if(!preg_match('/[A-Za-z0-9]{32}/', $name))
            {
                throw new \Exception(\Lang::get('asset::errors.controller.file-name', ['name' => $name]));
            }

            // It was valid, so verify the file name
            $filename = storage_path() . '/' . \Config::get('asset::cache.directory') . '/' . $type . '/' . $name;

            // Check that it exists
            if(!file_exists($filename))
            {
                throw new \Exception(\Lang::get('asset::controller.file-not-found', ['name' => $name]));
            }

            // Everything validates, so spit it out
            return file_get_contents($filename);
        }
        else
        {
            throw new \Exception(\Lang::get('asset::controller.invalid-type', ['type' => $type]));
        }
    }
}