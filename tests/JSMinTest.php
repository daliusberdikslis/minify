<?php

namespace Minify\Test;

use Exception;
use JSMin\JSMin;
use JSMin\UnterminatedStringException;
use JSMin\UnterminatedRegExpException;
use JSMin\UnterminatedCommentException;

class JSMinTest extends TestCase
{
    public function test1(): void
    {
        $src = file_get_contents(self::$test_files . '/js/before.js');
        $minExpected = file_get_contents(self::$test_files . '/js/before.min.js');
        $minOutput = JSMin::minify($src);
        self::assertSame($minExpected, $minOutput, 'Overall');
    }

    public function test2(): void
    {
        $src = file_get_contents(self::$test_files . '/js/issue144.js');
        $minExpected = file_get_contents(self::$test_files . '/js/issue144.min.js');
        $minOutput = JSMin::minify($src);
        self::assertSame($minExpected, $minOutput, 'Handle "+ ++a" syntax (Issue 144)');
    }

    public function test3(): void
    {
        $src = file_get_contents(self::$test_files . '/js/issue256.js');
        $minExpected = file_get_contents(self::$test_files . '/js/issue256.min.js');
        $minOutput = JSMin::minify($src);
        self::assertSame($minExpected, $minOutput, 'Handle \n!function()... (Issue 256)');
    }

    public function test4(): void
    {
        $have_overload = function_exists('mb_strlen') && ((int)ini_get('mbstring.func_overload') & 2);
        if (!$have_overload) {
            self::markTestSkipped();
        }

        $src = file_get_contents(self::$test_files . '/js/issue132.js');
        $minExpected = file_get_contents(self::$test_files . '/js/issue132.min.js');
        $minOutput = JSMin::minify($src);
        self::assertSame($minExpected, $minOutput, 'mbstring.func_overload shouldn\'t cause failure (Issue 132)');
    }

    public function test5(): void
    {
        $src = file_get_contents(self::$test_files . '/js/regexes.js');
        $minExpected = file_get_contents(self::$test_files . '/js/regexes.min.js');
        $minOutput = JSMin::minify($src);
        self::assertSame($minExpected, $minOutput, 'Identify RegExp literals');
    }

    /**
     * @param string $js
     * @param string $label
     * @param string $expClass
     * @param string $expMessage
     *
     * @dataProvider JSMinExceptionDataProvider
     */
    public function testJSMinException(string $js, string $label, string $expClass, string $expMessage): void
    {
        $eClass = $eMsg = '';
        try {
            JSMin::minify($js);
        } catch (Exception $e) {
            $eClass = get_class($e);
            $eMsg = $e->getMessage();
        }
        self::assertTrue($eClass === $expClass && $eMsg === $expMessage, 'Throw on ' . $label);
    }

    public function JSMinExceptionDataProvider(): array
    {
        // $js, $label, $expClass, $expMessage
        return array(
            array(
                '"Hello',
                'Unterminated String',
                UnterminatedStringException::class,
                "JSMin: Unterminated String at byte 5: \"Hello",
            ),

            array(
                "return /regexp\n}",
                'Unterminated RegExp',
                UnterminatedRegExpException::class,
                "JSMin: Unterminated RegExp at byte 14: /regexp\n",
            ),

            array(
                "return/regexp\n}",
                'Unterminated RegExp',
                UnterminatedRegExpException::class,
                "JSMin: Unterminated RegExp at byte 13: /regexp\n",
            ),

            array(
                ";return/regexp\n}",
                'Unterminated RegExp',
                UnterminatedRegExpException::class,
                "JSMin: Unterminated RegExp at byte 14: /regexp\n",
            ),

            array(
                ";return /regexp\n}",
                'Unterminated RegExp',
                UnterminatedRegExpException::class,
                "JSMin: Unterminated RegExp at byte 15: /regexp\n",
            ),

            array(
                "typeof/regexp\n}",
                'Unterminated RegExp',
                UnterminatedRegExpException::class,
                "JSMin: Unterminated RegExp at byte 13: /regexp\n",
            ),

            array(
                "/* Comment ",
                'Unterminated Comment',
                UnterminatedCommentException::class,
                "JSMin: Unterminated comment at byte 11: /* Comment ",
            ),
        );
    }
}
