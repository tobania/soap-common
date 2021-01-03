<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Builder;

use GoetasWebservices\SoapServices\Metadata\DependencyInjection\Compiler\CleanupPass;
use GoetasWebservices\SoapServices\Metadata\Envelope\Envelope;
use GoetasWebservices\SoapServices\Metadata\Headers\Handler\HeaderHandler;
use GoetasWebservices\Xsd\XsdToPhpRuntime\Jms\Handler\BaseTypesHandler;
use GoetasWebservices\Xsd\XsdToPhpRuntime\Jms\Handler\XmlSchemaDateHandler;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

abstract class SoapContainerBuilder
{
    /**
     * @var string
     */
    private $className = 'SoapContainer';

    /**
     * @var string
     */
    private $classNs = 'SoapServicesStub';

    /**
     * @var string
     */
    protected $configFile = 'config.yml';

    /**
     * @var string[]
     */
    protected $extensions = [];

    /**
     * @var string[]
     */
    protected $compilerPasses = [];

    public function __construct(?string $configFile = null)
    {
        $this->setConfigFile($configFile);
        $this->addCompilerPass(new CleanupPass());
    }

    public function setConfigFile(string $configFile): void
    {
        $this->configFile = $configFile;
    }

    protected function addExtension(ExtensionInterface $extension): void
    {
        $this->extensions[] = $extension;
    }

    protected function addCompilerPass(CompilerPassInterface $pass): void
    {
        $this->compilerPasses[] = $pass;
    }

    public function setContainerClassName(string $fqcn): void
    {
        $fqcn = strtr($fqcn, [
            '.' => '\\',
            '/' => '\\',
        ]);
        $pos = strrpos($fqcn, '\\');
        $this->className = substr($fqcn, $pos + 1);
        $this->classNs = substr($fqcn, 0, $pos);
    }

    /**
     * @param array $metadata
     */
    protected function getContainerBuilder(array $metadata = []): ContainerBuilder
    {
        $container = new ContainerBuilder();

        foreach ($this->extensions as $extension) {
            $container->registerExtension($extension);
        }

        foreach ($this->compilerPasses as $pass) {
            $container->addCompilerPass($pass);
        }

        $locator = new FileLocator('.');
        $loaders = [
            new YamlFileLoader($container, $locator),
            new XmlFileLoader($container, $locator),
        ];
        $delegatingLoader = new DelegatingLoader(new LoaderResolver($loaders));
        $delegatingLoader->load($this->configFile);

        // set the production soap metadata
        $container->setParameter('goetas_webservices.soap.metadata', $metadata);

        $container->compile();

        return $container;
    }

    /**
     * @return array
     */
    protected function fetchMetadata(ContainerInterface $debugContainer): array
    {
        $metadataReader = $debugContainer->get('goetas_webservices.soap.metadata_loader.dev');
        $wsdlMetadata = $debugContainer->getParameter('goetas_webservices.soap.config')['metadata'];
        $metadata = [];
        foreach (array_keys($wsdlMetadata) as $uri) {
            $metadata[$uri] = $metadataReader->load($uri);
        }

        return $metadata;
    }

    public function getDebugContainer(): ContainerBuilder
    {
        return $this->getContainerBuilder();
    }

    public function getProdContainer(): ContainerInterface
    {
        $ref = new \ReflectionClass(sprintf('%s\\%s', $this->classNs, $this->className));

        return $ref->newInstance();
    }

    public function dumpContainerForProd(string $dir, ContainerInterface $debugContainer): void
    {
        $metadata = $this->fetchMetadata($debugContainer);

        if (!$metadata) {
            throw new \Exception('Empty metadata can not be used for production');
        }

        $forProdContainer = $this->getContainerBuilder($metadata);
        $this->dump($forProdContainer, $dir);
    }

    private function dump(ContainerBuilder $container, string $dir): void
    {
        $dumper = new PhpDumper($container);
        $options = [
            'debug' => false,
            'class' => $this->className,
            'namespace' => $this->classNs,
        ];

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($dir . '/' . $this->className . '.php', $dumper->dump($options));
    }

    public static function createSerializerBuilderFromContainer(ContainerInterface $container, ?callable $handlersCallback = null, ?callable $listenersCallback = null, ?string $metadataDirPrefix = null): SerializerBuilder
    {
        $destinations = $container->getParameter('goetas_webservices.soap.config')['destinations_jms'];

        if (null !== $metadataDirPrefix) {
            $destinations = array_map(static function ($dir) use ($metadataDirPrefix) {
                return rtrim($metadataDirPrefix, '/') . '/' . $dir;
            }, $destinations);
        }

        return self::createSerializerBuilder($destinations, $handlersCallback, $listenersCallback);
    }

    /**
     * @param array $jmsMetadata
     */
    public static function createSerializerBuilder(array $jmsMetadata, ?callable $handlersCallback = null, ?callable $listenersCallback = null): SerializerBuilder
    {
        $jmsMetadata = array_merge(self::getMetadataForSoapEnvelope(), $jmsMetadata);

        $serializerBuilder = SerializerBuilder::create();

        $h = new HeaderHandler();
        $serializerBuilder->configureHandlers(static function (HandlerRegistryInterface $handler) use ($handlersCallback, $serializerBuilder, $h): void {
            $serializerBuilder->addDefaultHandlers();
            $handler->registerSubscribingHandler(new BaseTypesHandler()); // XMLSchema List handling
            $handler->registerSubscribingHandler(new XmlSchemaDateHandler()); // XMLSchema date handling
            $handler->registerSubscribingHandler($h);
            if ($handlersCallback) {
                call_user_func($handlersCallback, $handler);
            }
        });

        $serializerBuilder->configureListeners(static function (EventDispatcherInterface $d) use ($serializerBuilder, $h, $listenersCallback): void {
            $serializerBuilder->addDefaultListeners();
            $d->addSubscriber($h);
            if ($listenersCallback) {
                call_user_func($listenersCallback, $d);
            }
        });

        foreach ($jmsMetadata as $php => $dir) {
            $serializerBuilder->addMetadataDir($dir, $php);
        }

        return $serializerBuilder;
    }

    /**
     * @return string[]
     */
    public static function getMetadataForSoapEnvelope(): array
    {
        $ref = new \ReflectionClass(Envelope::class);

        return [
            'GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope12' => dirname($ref->getFileName()) . '/../Resources/metadata/jms12',
            'GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope' => dirname($ref->getFileName()) . '/../Resources/metadata/jms',
        ];
    }
}
