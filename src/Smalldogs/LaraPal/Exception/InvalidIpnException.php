<?php namespace Smalldogs\LaraPal\Exception;

class InvalidIpnException extends \Exception {}


\App::error(function(InvalidIpnException $e, $code, $fromConsole)
{
    if ( $fromConsole )
    {
        return 'Error '.$code.': '.$e->getMessage()."\n";
    }

    \Log::error('PayPal IPN verification failed: '. $e->getMessage());

    return '<h1>InvalidIpnException: Error ' . $code . '</h1><pre>' . $e->getMessage() . '</pre>';

});
