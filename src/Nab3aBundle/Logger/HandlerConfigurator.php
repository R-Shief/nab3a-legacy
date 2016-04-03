<?php

namespace Nab3aBundle\Logger;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class HandlerConfigurator
{
    use ContainerAwareTrait;

    /**
     * @var \Monolog\Formatter\FormatterInterface
     */
    private $formatter;

    public function __construct(FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter;
    }

    public function __invoke(HandlerInterface $handler)
    {
        if ($this->formatter) {
            $handler->setFormatter($this->formatter);
        }
    }
}
