<?php

namespace Nab3aBundle\Command;

use Nab3aBundle\Model\StreamParameters;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\ConstraintViolation;

class ValidationCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('validate')
          ->setDescription('checks that your streaming API parameters are allowed')
          ->addArgument('name', InputArgument::OPTIONAL, 'container parameter with filter parameters', 'default')
        ;
    }

    /**
     * This is all made up!
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return StreamParameters
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = 'nab3a.stream.'.$input->getArgument('name');
        $params = $this->container->get('nab3a.standalone.parameters')->get($name);
        $io = new SymfonyStyle($input, $output);

        $serializer = $this->container->get('serializer');
        $query = $serializer->denormalize($params['parameters'], StreamParameters::class);

        $validator = $this->container->get('validator');
        $errors = $validator->validate($query);

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $io->section($error->getPropertyPath());
            $io->error($error->getMessage());
        }

        if ($errors->count() === 0) {
            $io->success($input->getArgument('name').' is valid');
        }

        return $errors->count();
    }
}
