<?php
namespace GoetasWebservices\SoapServices\SoapCommon\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Generate extends Command
{
    private $debugContainer;

    protected function configure()
    {
        parent::configure();
        $this->setName('generate');
        $this->setDefinition([
            new InputArgument('config', InputArgument::REQUIRED, 'Config file location'),
            new InputArgument('dest-dir', InputArgument::REQUIRED, 'Config file location'),
            new InputOption('dest-class', null,  InputOption::VALUE_REQUIRED, 'Config file location'),
        ]);
    }

    /**
     * @return \GoetasWebservices\SoapServices\SoapCommon\Builder\SoapContainerBuilder
     */
    protected abstract function getContainerBuilder();

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $containerBuilder = $this->getContainerBuilder();

        $containerBuilder->setConfigFile($input->getArgument('config'));

        if ($input->getOption('dest-class')) {
            $containerBuilder->setContainerClassName($input->getOption('dest-class'));
        }

        $this->debugContainer = $containerBuilder->getDebugContainer();

        $wsdlMetadata = $this->debugContainer->getParameter('wsdl2php.config')['metadata'];

        $schemas = [];
        $portTypes = [];
        $wsdlReader = $this->debugContainer->get('goetas_webservices.wsdl2php.wsdl_reader');

        foreach (array_keys($wsdlMetadata) as $src) {
            $definitions = $wsdlReader->readFile($src);
            $schemas[] = $definitions->getSchema();
            $portTypes = array_merge($portTypes, $definitions->getPortTypes());
        }

        $soapReader = $this->debugContainer->get('goetas_webservices.wsdl2php.soap_reader');

        foreach (['php', 'jms'] as $type) {
            $converter = $this->debugContainer->get('goetas_webservices.xsd2php.converter.' . $type);
            $wsdlConverter = $this->debugContainer->get('goetas_webservices.wsdl2php.converter.' . $type);
            $items = $wsdlConverter->visitServices($soapReader->getServices());
            $items = array_merge($items, $converter->convert($schemas));

            $writer = $this->debugContainer->get('goetas_webservices.xsd2php.writer.' . $type);
            $writer->write($items);
        }
    }
}
