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
 * @see ZFCS_Tests_AbstractSniffTest
 */
require_once __DIR__ . '/../AbstractSniffTest.php';

/**
 * @category   ZFCS
 * @package    ZFCS_Tests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZFCS_Tests_Commenting_DisallowHashCommentsSniff
    extends ZFCS_Tests_AbstractSniffTest
{
    public function testErrorWithHashComment()
    {
        $this->assertSniffError(
            'ZFCS_Sniffs_Commenting_DisallowHashCommentsSniff',
            __DIR__ . '/assets/InvalidUseOfHashComments.php',
            2,
            'Hash comments are prohibited; found #This is a hash comment'
        );
    }
}
