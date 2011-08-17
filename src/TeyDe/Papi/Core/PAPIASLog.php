<?php

namespace TeyDe\Papi\Core;

class PAPIASLog
{
    public static function error($msg, $log="/dev/null", $asId="example_as")
    {
        PAPIASLog::doLog($msg, $log, $asId);
        echo $msg;
        throw new \RuntimeException($msg);
    }

    public static function doLog($msg, $log="/dev/null", $asId="example_as")
    {        
        if ($log != "/dev/null")
        {
            $emsg = date("d-M-Y H:i:s") . ", " . $asId . ": " . $msg . "\n";
            error_log($emsg, 3, $log);
        }
    }
}

