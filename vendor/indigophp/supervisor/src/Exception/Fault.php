<?php

/*
 * This file is part of the Indigo Supervisor package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Supervisor\Exception;

/**
 * Fault codes are taken from the source code, not the documentation
 * The most common ones are covered by the XML-RPC doc
 *
 * @link http://supervisord.org/api.html
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Fault extends \Exception
{
    /**
     * Fault responses
     */
    const UNKNOWN_METHOD        = 1;
    const INCORRECT_PARAMETERS  = 2;
    const BAD_ARGUMENTS         = 3;
    const SIGNATURE_UNSUPPORTED = 4;
    const SHUTDOWN_STATE        = 6;
    const BAD_NAME              = 10;
    const BAD_SIGNAL            = 11;
    const NO_FILE               = 20;
    const NOT_EXECUTABLE        = 21;
    const FAILED                = 30;
    const ABNORMAL_TERMINATION  = 40;
    const SPAWN_ERROR           = 50;
    const ALREADY_STARTED       = 60;
    const NOT_RUNNING           = 70;
    const SUCCESS               = 80;
    const ALREADY_ADDED         = 90;
    const STILL_RUNNING         = 91;
    const CANT_REREAD           = 92;

    /**
     * @var array
     */
    private static $exceptionMap = [
        1  => 'Indigo\Supervisor\Exception\Fault\UnknownMethod',
        2  => 'Indigo\Supervisor\Exception\Fault\IncorrectParameters',
        3  => 'Indigo\Supervisor\Exception\Fault\BadArguments',
        4  => 'Indigo\Supervisor\Exception\Fault\SignatureUnsupported',
        6  => 'Indigo\Supervisor\Exception\Fault\ShutdownState',
        10 => 'Indigo\Supervisor\Exception\Fault\BadName',
        11 => 'Indigo\Supervisor\Exception\Fault\BadSignal',
        20 => 'Indigo\Supervisor\Exception\Fault\NoFile',
        21 => 'Indigo\Supervisor\Exception\Fault\NotExecutable',
        30 => 'Indigo\Supervisor\Exception\Fault\Failed',
        40 => 'Indigo\Supervisor\Exception\Fault\AbnormalTermination',
        50 => 'Indigo\Supervisor\Exception\Fault\SpawnError',
        60 => 'Indigo\Supervisor\Exception\Fault\AlreadyStarted',
        70 => 'Indigo\Supervisor\Exception\Fault\NotRunning',
        80 => 'Indigo\Supervisor\Exception\Fault\Success',
        90 => 'Indigo\Supervisor\Exception\Fault\AlreadyAdded',
        91 => 'Indigo\Supervisor\Exception\Fault\StillRunning',
        92 => 'Indigo\Supervisor\Exception\Fault\CantReread',
    ];

    /**
     * Creates a new Fault
     *
     * If there is a mach for the fault code in the exception map then the matched exception will be returned
     *
     * @param string  $faultString
     * @param integer $faultCode
     *
     * @return self
     */
    public static function create($faultString, $faultCode)
    {
        if (!isset(self::$exceptionMap[$faultCode])) {
            return new self($faultString, $faultCode);
        }

        return new self::$exceptionMap[$faultCode]($faultString, $faultCode);
    }
}
