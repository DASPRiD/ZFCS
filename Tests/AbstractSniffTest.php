<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   ZFCS
 * @package    ZFCS_Tests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see PHP_CodeSniffer
 */
require_once 'PHP/CodeSniffer.php';

/**
 * @see PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * An abstract class that all sniff unit tests must extend.
 *
 * @category   ZFCS
 * @package    ZFCS_Tests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class ZFCS_Tests_AbstractSniffTest extends PHPUnit_Framework_TestCase
{
    /**
     * The PHP_CodeSniffer object used for testing.
     *
     * @var PHP_CodeSniffer
     */
    protected static $phpcs = null;

    /**
     * Sets up this unit test.
     *
     * @return void
     */
    protected function setUp()
    {
        if (self::$phpcs === null) {
            self::$phpcs = new PHP_CodeSniffer();
        }
    }

    /**
     * Assert a sniff error.
     *
     * @param  string  $sniffClass
     * @param  string  $file
     * @param  integer $expectedLine
     * @param  string  $expectedMessage
     * @return void
     */
    protected function assertSniffError($sniffClass, $file, $expectedLine, $expectedMessage)
    {
        $errors = $this->runSniff($sniffClass, $file)->getErrors();

        $this->assertSniffMessage($errors, $expectedLine, $expectedMessage);
    }

    /**
     * Assert a sniff warning.
     *
     * @param  string  $sniffClass
     * @param  string  $file
     * @param  integer $expectedLine
     * @param  string  $expectedMessage
     * @return void
     */
    protected function assertSniffWarning($sniffClass, $file, $expectedLine, $expectedMessage)
    {
        $warnings = $this->runSniff($sniffClass, $file)->getWarnings();

        $this->assertSniffMessage($warnings, $expectedLine, $expectedMessage);
    }

    /**
     * Assert a sniff message in a result.
     *
     * @param  array   $messages
     * @param  integer $expectedLine
     * @param  string  $expectedMessage
     * @return void
     */
    protected function assertSniffMessage($messages, $expectedLine, $expectedMessage)
    {
        foreach ($messages as $line => $columns) {
            if ($line !== $expectedLine) {
                continue;
            }

            foreach ($columns as $column => $results) {
                foreach ($results as $result) {
                    if ($result['message'] === $expectedMessage) {
                        return;
                    }
                }
            }
        }

        $this->fail(sprintf(
            'Expected message "%s" not found on line %d',
            $expectedMessage,
            $expectedLine
        ));
    }

    /**
     * Run a sniff on a given file.
     *
     * @param  string $sniffClass
     * @param  string $file
     * @return PHP_CodeSniffer_File
     */
    protected function runSniff($sniffClass, $file)
    {
        try {
            self::$phpcs->process($file, dirname(__DIR__), array($sniffClass));
        } catch (Exception $e) {
            $this->fail('An unexpected exception has been caught: ' . $e->getMessage());
        }

        $sniffs = self::$phpcs->getTokenSniffs();
        $files  = self::$phpcs->getFiles();

        return array_pop($files);
    }
}
