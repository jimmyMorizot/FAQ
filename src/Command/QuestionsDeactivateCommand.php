<?php

namespace App\Command;

use App\Repository\QuestionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QuestionsDeactivateCommand extends Command
{
    protected static $defaultName = 'app:questions:deactivate';
    protected static $defaultDescription = 'Deactivates outdated questions';

    // notre service pour accéder à la requête custom
    private $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;

        // On appelle le constructeur parent
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('limit', '-l', InputOption::VALUE_REQUIRED, 'Limit of days to deactivate a question');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // la limite est la valeur donnée OU 7 par défaut
        // @see https://en.wikipedia.org/wiki/Elvis_operator
        $limit = $input->getOption('limit') ?: 7;

        // la limite fournie doit être numérique
        if (!is_numeric($limit)) {
            $io->error('La limite de jours doit être un nombre');

            return Command::FAILURE;
        }

        // @todo pas de nombre négatifs, pas de nombre à virgule...

        $io->info('Traitement des questions à désactiver...');

        // Option 1 : On récupère toutes les Q. et on les traite en PHP
        // Ok mais why ? => ça peut être compliqué mais surtout, c'est le taf de SQL

        // Option 2 : On crée une requête SQL qui fait le job
        // => il est fait pour ça 

        // Appelons une requête custom qui fait cela
        $nbDeactivatedQuestions = $this->questionRepository->deactivateOutdated($limit);

        $io->success($nbDeactivatedQuestions . ' question(s) ont été désactivées.');

        return Command::SUCCESS;
    }
}
