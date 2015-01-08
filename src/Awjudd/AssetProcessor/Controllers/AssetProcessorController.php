<?php namespace Awjudd\AssetProcessor\Controllers;

use Carbon\Carbon;
use Config;
use Exception;
use Lang;
use Request;
use Response;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class AssetProcessorController extends Controller
{
    public function getIndex($type, $name)
    {
        $available = Config::get('assetprocessor::controller.content-types', array());

        // Validate the type
        if(isset($available[$type]))
        {
            // Verify that the name is an MD5
            if(!preg_match('/[A-Za-z0-9]{32}/', $name))
            {
                throw new Exception(Lang::get('assetprocessor::errors.controller.file-name', array('name' => $name)));
            }

            // It was valid, so verify the file name
            $filename = Config::get('assetprocessor::cache.external', Config::get('assetprocessor::config.cache.directory')) . '/' . $type . '/' . $name;

            // Check that it exists
            if(!file_exists($filename))
            {
                throw new Exception(Lang::get('assetprocessor::errors.controller.file-not-found', array('name' => $name)));
            }

            // When was the file last modified?
            $modified = Carbon::createFromTimeStamp(filemtime($filename));

            // Check if there is a modified date provided
            if(Request::header('If-None-Match') == $name)
            {
                return Response::make('', 304);
            }

            // Everything validates, so spit it out
            $output = file_get_contents($filename);

            return Response::make($output, 200, array(
                    'Content-Type' => $available[$type],
                    'Cache-Control' => 'max-age=9999, public',
                    'expires' => Carbon::now()->addYears(2),
                    'Last-Modified' => $modified,
                    'etag' => $name,
                )
            );
        }
        else
        {
            throw new Exception(Lang::get('assetprocessor::config.errors.controller.invalid-type', array('type' => $type)));
        }
    }
}