<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\DataFixtures\AppFixtures;

#[AsCommand(
    name: 'app:load-data',
    description: 'Load application data without fixtures bundle',
)]
class LoadDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Loading application data...');
        
        $fixtures = new AppFixtures();
        $fixtures->load($this->entityManager);
        
        $output->writeln('Data loaded successfully!');
        
        return Command::SUCCESS;
    }
}