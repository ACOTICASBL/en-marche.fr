<?php

namespace AppBundle\Command;

use AppBundle\Entity\VotePlace;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ImportVotePlacesCommand extends ContainerAwareCommand
{
    private const COLUMNS = [
        'nom_region',
        'code_insee_region',
        'code_insee_departement',
        'code_postal',
        'code_insee_commune',
        'nom_commune_bdv',
        'code_circonscription',
        'code_bdv',
        'nom_bdv',
        'adresse_bdv',
    ];

    /**
     * @var EntityManager
     */
    private $em;

    protected function configure()
    {
        $this
          ->setName('app:import:vote-places')
          ->addArgument('fileUrl', InputArgument::REQUIRED)
          ->setDescription(
            'Import vote places from file store in Google Storage'
          )
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $rows = $this->parseCSV($input->getArgument('fileUrl'), 'r');
        } catch (FileNotFoundException $exception) {
            $output->writeln(
              sprintf('%s file not found', $input->getArgument('fileUrl'))
            );

            return 1;
        }

        $this->em->beginTransaction();

        $this->createAndPersistVotePlace($rows);

        $this->em->flush();
        $this->em->commit();

        $output->writeln('Vote places load OK');
    }

    private function parseCSV(string $filename): array
    {
        $rows = [];
        if (false === ($handle = fopen($filename, 'r'))) {
            throw new FileNotFoundException(sprintf('% not found', $filename));
        }

        while (false !== ($data = fgetcsv($handle, 10000, ','))) {
            $row = array_map('trim', $data);

            if (self::COLUMNS[0] === $row[0]) {
                continue;
            }

            if (!empty($row[9])) {
                $rows[] = [
                    'code_postal' => $row[4],
                    'nom_commune_bdv' => $row[6],
                    'code_bdv' => $row[8],
                    'nom_bdv' => $row[9],
                    'adresse_bdv' => $row[10],
                ];
            }
        }

        fclose($handle);

        return $rows;
    }

    private function createAndPersistVotePlace(array $rows): void
    {
        foreach ($rows as $row) {
            if ($this->em->getRepository(VotePlace::class)->findOneByCode($row['code_bdv'])) {
                continue;
            }

            $votePlace = new VotePlace();
            $votePlace->setName($row['nom_bdv']);
            $votePlace->setAddress($row['adresse_bdv']);
            $votePlace->setPostalCode(false !== strpos($row['code_postal'], '/')
                ? substr($row['code_postal'], 0, strpos($row['code_postal'], '/'))
                : $row['code_postal']
            );
            $votePlace->setCity($row['nom_commune_bdv']);
            $votePlace->setCode($row['code_bdv']);

            $this->em->persist($votePlace);
        }

        $this->em->flush();
    }
}
