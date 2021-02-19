<?php

namespace Minify\Test;

use Minify_Build;
use Minify_Source;

class MinifyBuildTest extends TestCase
{
    public function test(): void
    {
        $file1 = self::$test_files . '/css/paths_prepend.css';
        $file2 = self::$test_files . '/css/styles.css';
        $maxTime = max(filemtime($file1), filemtime($file2));

        $b = new Minify_Build((array)$file1);
        self::assertEquals($b->lastModified, filemtime($file1), 'single file path');

        $b = new Minify_Build(array($file1, $file2));
        self::assertEquals($maxTime, $b->lastModified, 'multiple file paths');

        $b = new Minify_Build(array($file1, new Minify_Source(array('filepath' => $file2))));

        self::assertEquals($maxTime, $b->lastModified, 'file path and a Minify_Source');
        self::assertEquals("/path?{$maxTime}", $b->uri('/path'), 'uri() with no querystring');
        self::assertEquals("/path?hello&amp;{$maxTime}", $b->uri('/path?hello'), 'uri() with existing querystring');
    }
}
