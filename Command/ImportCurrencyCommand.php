<?php

namespace Lexik\Bundle\CurrencyBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\CurrencyBundle\Adapter\AdapterCollectorInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class ImportCurrencyCommand extends Command
{
    private $doctrine;
    private $adapterCollector;
    private $currencyClass;

    public function __construct(ManagerRegistry $doctrine, AdapterCollectorInterface $adapterCollector, $currencyClass)
    {
        $this->doctrine = $doctrine;
        $this->adapterCollector = $adapterCollector;
        $this->currencyClass = $currencyClass;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('lexik:currency:import')
            ->setDescription('Import currency rate')
            ->addArgument('adapter', InputArgument::REQUIRED, 'Adapter to import in database')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adapter = $this->adapterCollector
            ->get($input->getArgument('adapter'));
        $adapter->attachAll();

        // Persist currencies
        $entityManagerName = $input->getOption('em');
        $em = $this->doctrine->getManager($entityManagerName);

        $repository = $em->getRepository($this->currencyClass);

        foreach ($adapter as $value) {
            // Check if already exist
            $currency = $repository->findOneBy(array(
                'code' => $value->getCode(),
            ));

            if (!$currency) {
                $currency = $value;
                $em->persist($currency);

                $output->writeln(sprintf('<comment>Add: %s = %s</comment>', $currency->getCode(), $currency->getRate()));
            } else {
                $currency->setRate($value->getRate());

                $output->writeln(sprintf('<comment>Update: %s = %s</comment>', $currency->getCode(), $currency->getRate()));
            }
        }

        $em->flush();
    }
}
