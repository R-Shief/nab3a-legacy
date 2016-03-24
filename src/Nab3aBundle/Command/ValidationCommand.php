<?php

namespace Nab3aBundle\Command;

use Nab3aBundle\Model\StreamParameters;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Yaml\Yaml;

class ValidationCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();
        $this
          ->setName('validate')
          ->setDescription('checks that your streaming API parameters are allowed')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $serializer = $this->container->get('serializer');
        $query = $serializer->denormalize($this->params['parameters'], StreamParameters::class);

        $validator = $this->container->get('validator');
        $errors = $validator->validate($query);

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $io->section($error->getPropertyPath());
            $io->error($error->getMessage());
        }

        if ($errors->count() === 0) {
            $io->success($input->getArgument('name').' is valid');
            if ($io->isVerbose()) {
                $output->writeln(Yaml::dump($this->params));
            }
        }

        return $errors->count();
    }
}
