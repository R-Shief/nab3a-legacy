<?php

namespace Nab3aBundle\Output;

use Evenement\EventEmitterInterface;
use Symfony\Component\Filesystem\Filesystem;

class BatchOutput
{
    public function __construct($pattern, Filesystem $filesystem)
    {
        $this->pattern = $pattern;
        $this->filesystem = $filesystem;
    }

    public function attachEvents(EventEmitterInterface $emitter)
    {
        $emitter->on('tweet', function ($message) {
            $data = \GuzzleHttp\json_decode($message, true);

            $replacements = array();
            $replacements['{id}'] = $data['id'];

            $filename = strtr($this->pattern, $replacements);
            $this->filesystem->dumpFile($filename, $message);
        });
    }
}
