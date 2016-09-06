<?php
namespace GoetasWebservices\SoapServices\SoapCommon;

use Doctrine\Instantiator\Instantiator;
use GoetasWebservices\SoapServices\Faults\MustUnderstandException;
use GoetasWebservices\SoapServices\Faults\ServerException;
use GoetasWebservices\SoapServices\Faults\SoapServerException;
use GoetasWebservices\SoapServices\Serializer\Handler\HeaderHandlerInterface;
use GoetasWebservices\SoapServices\SoapEnvelope;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Serializer;

class SoapCommon
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var array
     */
    protected $serviceDefinition;

    public function __construct(array $serviceDefinition, Serializer $serializer)
    {
        $this->serializer = $serializer;
        $this->serviceDefinition = $serviceDefinition;
    }

    protected function getXmlNamesDescription($object)
    {
        $factory = $this->serializer->getMetadataFactory();
        $classMetadata = $factory->getMetadataForClass(get_class($object));
        return "{{$classMetadata->xmlRootNamespace}}$classMetadata->xmlRootName";
    }

    protected function wrapResult($input, $class)
    {
        if (!$input instanceof $class) {
            $instantiator = new Instantiator();
            $factory = $this->serializer->getMetadataFactory();
            $previous = null;
            $previousProperty = null;
            $nextClass = $class;
            $originalInput = $input;
            $i = 0;
            while ($i++ < 4) {
                /**
                 * @var $classMetadata ClassMetadata
                 */
                if ($previousProperty && in_array($nextClass, ['double', 'string', 'float', 'integer', 'boolean'])) {
                    $previousProperty->setValue($previous, $originalInput);
                    break;
                }
                $classMetadata = $factory->getMetadataForClass($nextClass);
                if ($input === null && !$classMetadata->propertyMetadata) {
                    return $instantiator->instantiate($classMetadata->name);
                } elseif (!$classMetadata->propertyMetadata) {
                    throw new \Exception("Can not determine how to associate the message");
                }
                $instance = $instantiator->instantiate($classMetadata->name);
                /**
                 * @var $propertyMetadata PropertyMetadata
                 */
                $propertyMetadata = reset($classMetadata->propertyMetadata);

                if ($previous) {
                    $previousProperty->setValue($previous, $instance);
                } else {
                    $input = $instance;
                }
                if ($originalInput instanceof $propertyMetadata->type['name']) {
                    $propertyMetadata->setValue($instance, $originalInput);
                    break;
                }
                $previous = $instance;
                $nextClass = $propertyMetadata->type['name'];
                $previousProperty = $propertyMetadata;
            }
        }

        return $input;
    }
}
