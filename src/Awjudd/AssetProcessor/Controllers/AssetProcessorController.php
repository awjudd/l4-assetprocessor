<?php namespace Awjudd\AssetProcessor\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class AssetProcessorController extends Controller
{
    public function getIndex($type, $name)
    {
        $available = \Config::get('assetprocessor::controller.content-types', array());

        // Validate the type
        if(isset($available[$type]))
        {
            // Verify that the name is an MD5
            if(!preg_match('/[A-Za-z0-9]{32}/', $name))
            {
                throw new \Exception(\Lang::get('assetprocessor::errors.controller.file-name', ['name' => $name]));
            }

            // It was valid, so verify the file name
            $filename = storage_path() . '/' . \Config::get('assetprocessor::cache.directory') . '/' . $type . '/' . $name;

            // Check that it exists
            if(!file_exists($filename))
            {
                throw new \Exception(\Lang::get('assetprocessor::errors.controller.file-not-found', ['name' => $name]));
            }

            // Everything validates, so spit it out
            $output = file_get_contents($filename);

            return \Response::make($output, 200, array('Content-Type' => $available[$type]));
        }
        else
        {
            throw new \Exception(\Lang::get('assetprocessor::errors.controller.invalid-type', ['type' => $type]));
        }
    }
}