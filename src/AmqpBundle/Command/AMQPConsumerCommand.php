<?php

namespace M6Web\Bundle\AmqpBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use M6Web\Bundle\AmqpBundle\Amqp\Consumer;

/**
 * Command to run consumer and execute consumer as a service
 */
class AMQPConsumerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('m6web:rabbitmq:consumer')
            ->setDescription('Run consumer as a command')
            ->addArgument('consumer-name', InputArgument::REQUIRED, 'Consumer name')
            ->addOption('count', 'c', InputOption::VALUE_REQUIRED, 'How many messages to consume before exit. 0 - do not exit', 0);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Consumer $consumer */
        $consumer = $this->getContainer()->get(sprintf('m6_web_amqp.consumer.%s', $input->getArgument('consumer-name')));
        $consumed = 0;
        while ($input->getOption('count') == 0 || $consumed < $input->getOption('count')) {
            $consumer->consume();

            $consumed++;
        }
    }
}
