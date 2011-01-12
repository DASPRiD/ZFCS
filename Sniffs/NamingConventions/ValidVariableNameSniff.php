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
 * @package    ZFCS_Sniffs
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * This sniff checks for propper variable naming.
 *
 * All variables should be camel-cased and *should* not contain numbers.
 *
 * @category   ZFCS
 * @package    ZFCS_Sniffs
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZFCS_Sniffs_NamingConventions_ValidVariableNameSniff
	extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{
    /**
     * Tokens to ignore so that we can find a DOUBLE_COLON.
     *
     * @var array
     */
    private $_ignore = array(
        T_WHITESPACE,
        T_COMMENT,
    );

    /**
     * processVariable(): defined by PHP_CodeSniffer_Standards_AbstractVariableSniff class
     *
     * @see    PHP_CodeSniffer_Sniff::processVariable()
     * @param  PHP_CodeSniffer_File $phpcsFile
     * @param  integer              $stackPtr
     * @return void
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        $phpReservedVars = array(
            '_SERVER',
            '_GET',
            '_POST',
            '_REQUEST',
            '_SESSION',
            '_ENV',
            '_COOKIE',
            '_FILES',
            'GLOBALS',
        );

        if (in_array($varName, $phpReservedVars) === true) {
            return;
        }

        $objOperator = $phpcsFile->findNext(array(T_WHITESPACE), ($stackPtr + 1), null, true);

        if ($tokens[$objOperator]['code'] === T_OBJECT_OPERATOR) {
            // Check to see if we are using a variable from an object.
            $var = $phpcsFile->findNext(array(T_WHITESPACE), ($objOperator + 1), null, true);

            if ($tokens[$var]['code'] === T_STRING) {
                // Either a var name or a function call, so check for bracket.
                $bracket = $phpcsFile->findNext(array(T_WHITESPACE), ($var + 1), null, true);

                if ($tokens[$bracket]['code'] !== T_OPEN_PARENTHESIS) {
                    $objVarName = $tokens[$var]['content'];

                    if (PHP_CodeSniffer::isCamelCaps($objVarName, false, true, false) === false) {
                        $error = 'Variable "%s" is not in valid camel caps format';
                        $data  = array($originalVarName);
                        $phpcsFile->addError($error, $var, 'NotCamelCaps', $data);
                    } else if (preg_match('|\d|', $objVarName)) {
                        $warning = 'Variable "%s" contains numbers but this is discouraged';
                        $data    = array($originalVarName);
                        $phpcsFile->addWarning($warning, $stackPtr, 'ContainsNumbers', $data);
                    }
                }
            }
        }

        if (PHP_CodeSniffer::isCamelCaps($varName, false, true, false) === false) {
            $error = 'Variable "%s" is not in valid camel caps format';
            $data  = array($originalVarName);
            $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $data);
        } else if (preg_match('|\d|', $varName)) {
            $warning = 'Variable "%s" contains numbers but this is discouraged';
            $data    = array($originalVarName);
            $phpcsFile->addWarning($warning, $stackPtr, 'ContainsNumbers', $data);
        }
    }

    /**
     * processMemberVar(): defined by PHP_CodeSniffer_Standards_AbstractVariableSniff class
     *
     * @see    PHP_CodeSniffer_Sniff::processMemberVar()
     * @param  PHP_CodeSniffer_File $phpcsFile 
     * @param  integer              $stackPtr 
     * @return void
     */
    protected function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        if (PHP_CodeSniffer::isCamelCaps($varName, false, $public, false) === false) {
            $error = 'Variable "%s" is not in valid camel caps format';
            $data  = array($varName);
            $phpcsFile->addError($error, $stackPtr, 'MemberVarNotCamelCaps', $data);
        } else if (preg_match('|\d|', $varName)) {
            $warning = 'Variable "%s" contains numbers but this is discouraged';
            $data    = array($varName);
            $phpcsFile->addWarning($warning, $stackPtr, 'MemberVarContainsNumbers', $data);
        }
    }

    /**
     * processVariableInString(): defined by PHP_CodeSniffer_Standards_AbstractVariableSniff class
     *
     * @see    PHP_CodeSniffer_Sniff::processVariableInString()
     * @param  PHP_CodeSniffer_File $phpcsFile 
     * @param  integer              $stackPtr 
     * @return void
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $phpReservedVars = array(
            '_SERVER',
            '_GET',
            '_POST',
            '_REQUEST',
            '_SESSION',
            '_ENV',
            '_COOKIE',
            '_FILES',
            'GLOBALS',
        );

        if (preg_match_all('|[^\\\]\$([a-zA-Z0-9_]+)|', $tokens[$stackPtr]['content'], $matches) !== 0) {
            foreach ($matches[1] as $varName) {
                if (in_array($varName, $phpReservedVars) === true) {
                    continue;
                }

                if (PHP_CodeSniffer::isCamelCaps($varName, false, true, false) === false) {
                    $varName = $matches[0];
                    $error   = 'Variable "%s" is not in valid camel caps format';
                    $data    = array($originalVarName);
                    $phpcsFile->addError($error, $stackPtr, 'StringVarNotCamelCaps', $data);
                } else if (preg_match('|\d|', $varName)) {
                    $warning = 'Variable "%s" contains numbers but this is discouraged';
                    $data    = array($originalVarName);
                    $phpcsFile->addWarning($warning, $stackPtr, 'StringVarContainsNumbers', $data);
                }
            }
        }
    }
}

