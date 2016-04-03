<?php

function json_stream_callback($callback)
{
    return function ($chunk) use ($callback) {
        static $buffer = '';

        while ($chunk !== '') {
            $pos = strpos($chunk, PHP_EOL);
            // no end found in chunk => must be part of segment, wait for next chunk
            if ($pos === false) {
                $buffer .= $chunk;
                break;
            }
            // possible end found in chunk => select possible segment from buffer, keep remaining chunk
            $buffer .= substr($chunk, 0, $pos + 1);
            $chunk = substr($chunk, $pos + 1);

            $callback($buffer);
            $buffer = '';
        }
    };
}
