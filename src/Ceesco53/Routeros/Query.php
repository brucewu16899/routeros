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
 * Refers to transmitter direction constants.
 */
use PEAR2\Net\Transmitter as T;

/**
 * Represents a query for RouterOS requests.
 * 
 * @category Net
 * @package  PEAR2_Net_RouterOS
 * @author   Vasil Rangelov <boen.robot@gmail.com>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link     http://pear2.php.net/PEAR2_Net_RouterOS
 */
class Query {

    /**
     * Checks if the property exists.
     */
    const ACTION_EXIST = '';
    
    /**
     * Checks if the property does not exist.
     */
    const ACTION_NOT_EXIST = '-';
    
    /**
     * Checks if the property equals a certain value.
     */
    const ACTION_EQUALS = '=';
    
    /**
     * Checks if the property is less than a certain value.
     */
    const ACTION_LESS_THAN = '<';
    
    /**
     * Checks if the property is greather than a certain value.
     */
    const ACTION_GREATHER_THAN = '>';

    /**
     * @var array An array of the words forming the query. Each value is an
     *     array with the first member being the predicate (action and name),
     *     and the second member being the value for the predicate.
     */
    protected $words = array();

    /**
     * This class is not to be instantiated normally, but by static methods
     * instead. Use {@link where()} to create an instance of it.
     */
    private function __construct()
    {
        
    }

    /**
     * Sanitizes the action of a condition.
     * 
     * @param string $action The action to sanitize.
     * 
     * @return string The sanitized action.
     */
    protected static function sanitizeAction($action)
    {
        $action = (string) $action;
        switch ($action) {
        case Query::ACTION_EXIST:
        case Query::ACTION_NOT_EXIST:
        case Query::ACTION_EQUALS:
        case Query::ACTION_LESS_THAN:
        case Query::ACTION_GREATHER_THAN:
            return $action;
        default:
            throw new UnexpectedValueException(
                'Unknown action specified',
                UnexpectedValueException::CODE_ACTION_UNKNOWN,
                null,
                $action
            );
        }
    }

    /**
     * Creates a new query with an initial condition.
     * 
     * @param string $name   The name of the property to test.
     * @param string $value  The value to test against. Not required for
     *     existence tests.
     * @param string $action One of the ACTION_* constants. Describes the
     *     operation to perform.
     * 
     * @return self|Query The query object.
     */
    public static function where(
        $name,
        $value = null,
        $action = self::ACTION_EXIST
    ) {
        $query = new static;
        return $query->addWhere($name, $value, $action);
    }

    /**
     * Negates the query.
     * 
     * @return self|Query The query object.
     */
    public function not()
    {
        $this->words[] = array('#!', null);
        return $this;
    }

    /**
     * Adds a condition as an alternative to the query.
     * 
     * @param string $name   The name of the property to test.
     * @param string $value  The value to test against. Not required for
     *     existence tests.
     * @param string $action One of the ACTION_* constants. Describes the
     *     operation to perform.
     * 
     * @return self|Query The query object.
     */
    public function orWhere($name, $value = null, $action = self::ACTION_EXIST)
    {
        $this->addWhere($name, $value, $action)->words[] = array('#|', null);
        return $this;
    }

    /**
     * Adds a condition in addition to the query.
     * 
     * @param string $name   The name of the property to test.
     * @param string $value  The value to test against. Not required for
     *     existence tests.
     * @param string $action One of the ACTION_* constants. Describes the
     *     operation to perform.
     * 
     * @return self|Query The query object.
     */
    public function andWhere($name, $value = null, $action = self::ACTION_EXIST)
    {
        $this->addWhere($name, $value, $action)->words[] = array('#&', null);
        return $this;
    }

    /**
     * Sends the query over a communicator.
     * 
     * @param Communicator $com The communicator to send the query over.
     * 
     * @return int The number of bytes sent.
     */
    public function send(Communicator $com)
    {
        if ($com->getTransmitter()->isPersistent()) {
            $old = $com->getTransmitter()->lock(T\Stream::DIRECTION_SEND);
            $bytes = $this->_send($com);
            $com->getTransmitter()->lock($old, true);
            return $bytes;
        }
        return $this->_send($com);
    }

    /**
     * Sends the query over a communicator.
     * 
     * The only difference with the non private equivalent is that this one does
     * not do locking.
     * 
     * @param Communicator $com The communicator to send the query over.
     * 
     * @return int The number of bytes sent.
     */
    private function _send(Communicator $com)
    {
        if (!$com->getTransmitter()->isAcceptingData()) {
            throw new SocketException(
                'Transmitter is invalid. Sending aborted.',
                SocketException::CODE_UNACCEPTING_QUERY
            );
        }
        $bytes = 0;
        foreach ($this->words as $queryWord) {
            list($predicate, $value) = $queryWord;
            $prefix = '?' . $predicate;
            if (null === $value) {
                $bytes += $com->sendWord($prefix);
            } else {
                $prefix .= '=';
                if (is_string($value)) {
                    $bytes += $com->sendWord($prefix . $value);
                } else {
                    $bytes += $com->sendWordFromStream($prefix, $value);
                }
            }
        }
        return $bytes;
    }

    /**
     * Adds a condition.
     * 
     * @param string $name   The name of the property to test.
     * @param string $value  The value to test against. Not required for
     *     existence tests.
     * @param string $action One of the ACTION_* constants. Describes the
     *     operation to perform.
     * 
     * @return self|Query The query object.
     */
    protected function addWhere($name, $value, $action)
    {
        $this->words[] = array(
            static::sanitizeAction($action)
            . Message::sanitizeArgumentName($name),
            (null === $value ? null : Message::sanitizeArgumentValue($value))
        );
        return $this;
    }
}
