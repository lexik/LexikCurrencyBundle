<?php

namespace Lexik\Bundle\CurrencyBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\CurrencyBundle\Adapter\AbstractCurrencyAdapter;
use Lexik\Bundle\CurrencyBundle\Adapter\AdapterCollector;
use Lexik\Bundle\CurrencyBundle\Adapter\DoctrineCurrencyAdapter;
use Lexik\Bundle\CurrencyBundle\Entity\Currency;
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
    /**
     * @param class-string $currencyClass
     */
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private AdapterCollector $adapterCollector,
        private string $currencyClass
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $adapterName */
        $adapterName = $input->getArgument('adapter');
        $adapter = $this->adapterCollector->get($adapterName);
        $adapter->attachAll();

        /** @var string $managerName */
        $managerName = $input->getOption('em');
        $entityManagerName = $managerName;
        $em = $this->managerRegistry->getManager($entityManagerName);

        $repository = $em->getRepository($this->currencyClass);

        foreach ($adapter as $value) {
            $currency = $repository->findOneBy([
                'code' => $value->getCode(),
            ]);

            /** @var Currency|null $currency */
            if (!$currency) {
                $currency = $value;
                $em->persist($currency);

                $output->writeln(
                    sprintf('<comment>Add: %s = %s</comment>', $currency->getCode(), $currency->getRate())
                );
            } else {
                $currency->setRate($value->getRate());

                $output->writeln(
                    sprintf('<comment>Update: %s = %s</comment>', $currency->getCode(), $currency->getRate())
                );
            }
        }

        $em->flush();

        return Command::SUCCESS;
    }
}
