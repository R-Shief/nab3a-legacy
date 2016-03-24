<?php

namespace Nab3aBundle\Output;

use Evenement\EventEmitterInterface;
use Nab3aBundle\Evenement\PluginInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileOutputPlugin implements PluginInterface
{
    const TWITTER_DATE_FORMAT = 'D M j H:i:s P Y';

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct($pattern, Filesystem $filesystem)
    {
        $this->pattern = $pattern;
        $this->filesystem = $filesystem;
    }

    public function attachEvents(EventEmitterInterface $emitter)
    {
        $emitter->on('tweet', function ($message) {
            $data = json_decode($message, true);

            $replacements = array();
            $replacements['{created_at.date}'] = \DateTime::createFromFormat(self::TWITTER_DATE_FORMAT, $data['created_at'])->format('Y-m-d');
            $replacements['{created_at.time}'] = \DateTime::createFromFormat(self::TWITTER_DATE_FORMAT, $data['created_at'])->format('H:i');
            $replacements['{user.screen_name}'] = $data['user']['screen_name'];
            $replacements['{id}'] = $data['id'];

            $filename = strtr($this->pattern, $replacements);
            $this->filesystem->dumpFile($filename, $message);
        });
    }
}
