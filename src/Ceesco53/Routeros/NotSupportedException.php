<?php namespace Ceesco53\Routeros;

/**
 * RouterOS API client implementation.

 * 
 * RouterOS is the flag product of the company MikroTik and is a powerful router software. One of its many abilities is to allow control over it via an API. This package provides a client for that API, in turn allowing you to use PHP to control RouterOS hosts.
 * 
 * PHP version 5
 * 
 * @category  Net
 * @package   PEAR2_Net_RouterOS
 * @author    Vasil Rangelov <boen.robot@gmail.com>
 * @copyright 2011 Vasil Rangelov
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @version   1.0.0b4
 * @link      http://pear2.php.net/PEAR2_Net_RouterOS
 */


/**
 * Exception thrown when encountering something not supported by RouterOS or
 * this package.
 * 
 * @category Net
 * @package  PEAR2_Net_RouterOS
 * @author   Vasil Rangelov <boen.robot@gmail.com>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link     http://pear2.php.net/PEAR2_Net_RouterOS
 */
class NotSupportedException extends \Exception implements Exception {

    const CODE_CONTROL_BYTE = 1601;

    /**
     * @var mixed The unsuppported value.
     */
    private $_value;

    /**
     * Creates a new NotSupportedException.
     * 
     * @param string     $message  The Exception message to throw.
     * @param int        $code     The Exception code.
     * @param \Exception $previous The previous exception used for the exception
     *     chaining.
     * @param integer      $value    The unsupported value.
     */
    public function __construct(
        $message,
        $code = 0,
        $previous = null,
        $value = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->_value = $value;
    }

    /**
     * Gets the unsupported value.
     * 
     * @return mixed The unsupported value.
     */
    public function getValue()
    {
        return $this->_value;
    }

    // @codeCoverageIgnoreStart
    // String representation is not reliable in testing

    /**
     * Returns a string representation of the exception.
     * 
     * @return string The exception as a string.
     */
    public function __toString()
    {
        return parent::__toString() . "\nValue:{$this->_value}";
    }

    // @codeCoverageIgnoreEnd
}
