<?php

/**
 * Wrapper for network stream functionality.

 *
 * PHP has built in support for various types of network streams, such as HTTP and TCP sockets. One problem that arises with them is the fact that a single fread/fwrite call might not read/write all the data you intended, regardless of whether you're in blocking mode or not. While the PHP manual offers a workaround in the form of a loop with a few variables, using it every single time you want to read/write can be tedious.

This package abstracts this away, so that when you want to get exactly N amount of bytes, you can be sure the upper levels of your app will be dealing with N bytes. Oh, and the functionality is nicely wrapped in an object (but that's just the icing on the cake).
 *
 * PHP version 5
 *
 * @category  Net
 * @package   PEAR2_Net_Transmitter
 * @author    Vasil Rangelov <boen.robot@gmail.com>
 * @copyright 2011 Vasil Rangelov
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @version   1.0.0a4
 * @link      http://pear2.php.net/PEAR2_Net_Transmitter
 */
/**
 * The namespace declaration.
 */
namespace PEAR2\Net\Transmitter;
require_once('Exception.php');
/**
 * Exception thrown when something goes wrong with the connection.
 *
 * @category Net
 * @package  PEAR2_Net_Transmitter
 * @author   Vasil Rangelov <boen.robot@gmail.com>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link     http://pear2.php.net/PEAR2_Net_Transmitter
 */
class SocketException extends \RuntimeException implements Exception
{

    /**
     * @var int The system level error code.
     */
    protected $error_no;

    /**
     * @var string The system level error message.
     */
    protected $error_str;

    /**
     * Creates a new socket exception.
     *
     * @param string     $message   The Exception message to throw.
     * @param int        $code      The Exception code.
     * @param \Exception $previous  The previous exception used for the
     *     exception chaining.
     * @param int        $error_no  The system level error number.
     * @param string     $error_str The system level error message.
     */
    public function __construct(
        $message = '',
        $code = 0,
        $previous = null,
        $error_no = null,
        $error_str = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->error_no = $error_no;
        $this->error_str = $error_str;
    }

    /**
     * Gets the system level error code on the socket.
     *
     * @return int The system level error number.
     */
    public function getSocketErrorNumber()
    {
        return $this->error_no;
    }

    // @codeCoverageIgnoreStart
    // Unreliable in testing.

    /**
     * Gets the system level error message on the socket.
     *
     * @return string The system level error message.
     */
    public function getSocketErrorMessage()
    {
        return $this->error_str;
    }

    /**
     * Returns a string representation of the exception.
     *
     * @return string The exception as a string.
     */
    public function __toString()
    {
        $result = parent::__toString();
        if (null !== $this->getSocketErrorNumber()) {
            $result .= "\nSocket error number:" . $this->getSocketErrorNumber();
        }
        if (null !== $this->getSocketErrorMessage()) {
            $result .= "\nSocket error message:"
                . $this->getSocketErrorMessage();
        }
        return $result;
    }
    // @codeCoverageIgnoreEnd
}
