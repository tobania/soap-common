<?php
namespace GoetasWebservices\SoapServices\SoapCommon;

use Doctrine\Instantiator\Instantiator;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Serializer;

trait ResultWrapperTrait
{
    /**
     * @param SerializerInterface $serializer
     * @param object|null $input
     * @param string $class
     * @return object
     * @throws \Exception
     */
    protected function wrapResult(Serializer $serializer, $input, $class)
    {
        if (!$input instanceof $class) {
            $instantiator = new Instantiator();
            $factory = $serializer->getMetadataFactory();
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
