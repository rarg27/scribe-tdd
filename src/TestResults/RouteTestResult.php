<?php

namespace AjCastro\ScribeTdd\TestResults;

use AjCastro\ScribeTdd\Tests\ExampleCreator;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\File;

class RouteTestResult
{
    protected static $cache = [];

    public static function getTestResultForRoute(Route $route)
    {
        $dir = ExampleCreator::writeDir($route);
        if ($result = static::$cache[$dir] ?? null) {
            return $result;
        }

        if (File::missing($dir)) {
            return [];
        }

        return static::$cache[$dir] = static::loadTestResults($dir);
    }

    public static function loadTestResults($dir)
    {
        $result = [
            'url_params'   => [],
            'query_params' => [],
            'body_params'  => [],
            'responses'    => [],
        ];

        $files = File::files($dir);

        foreach($files as $file) {
            $array = static::decodeFile($file->getPathname());

            $result = array_merge($result, $array);
            $result['url_params'] = $result['url_params'] + ($array['url_params'] ?? []);
            $result['query_params'] = $result['query_params'] + ($array['query_params'] ?? []);
            $result['body_params'] = $result['body_params'] + ($array['body_params'] ?? []);
            $result['responses'] = array_merge($result['responses'], $array['responses'] ?? []);
        }

        return $result;
    }

    public static function decodeFile($filepath)
    {
        return json_decode(File::get($filepath), true);
    }
}
