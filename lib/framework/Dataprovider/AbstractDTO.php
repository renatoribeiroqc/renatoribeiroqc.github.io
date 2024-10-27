<?php

namespace Lib\Framework\Dataprovider;

use Exception;
use ReflectionClass;
use ReflectionProperty;

abstract class AbstractDTO
{
    public function __construct(array $properties)
    {
        $className = $this->reflectionClass()->getName();

        $missingPropertiesResult = $this->getMissingProperties($properties); // Verifica se faltam propriedades

        if ($missingPropertiesResult->isInvalid) {
            $missingProperties = implode(',', $missingPropertiesResult->missingProperties);

            throw new Exception(
                "Missing/Wrong Properties Set: $missingProperties in DTO: $className"
            );
        }

        foreach ($properties as $propertyName => $propertyValue) {
            if ($this->isInvalidProperty($propertyName)) {
                throw new Exception("Invalid Property: $propertyName in DTO: $className");
            }

            $this->validatePropertyType($propertyName, $propertyValue); // Verifica se o valor corresponde ao tipo esperado

            $this->{$propertyName} = $propertyValue;
        }
    }

    private function getMissingProperties(array $properties): object
    {
        $declaredAndDefinedProperties = array_merge(
            array_keys($properties),
            array_keys($this->reflectionClass()->getDefaultProperties())
        );

        $validProperties = $this->validProperties(); // Propriedades da classe refletida

        return (object)[
            'isInvalid' => array_diff($declaredAndDefinedProperties, $validProperties)
                != array_diff($validProperties, $declaredAndDefinedProperties),
            'missingProperties' => array_merge(
                array_diff($declaredAndDefinedProperties, $validProperties),
                array_diff($validProperties, $declaredAndDefinedProperties)
            ),
        ];
    }

    private function isInvalidProperty(string $propertyName): bool
    {
        return !in_array($propertyName, $this->validProperties());
    }

    private function validProperties(): array
    {
        return array_map(
            function (ReflectionProperty $value): string {
                return $value->getName();
            },
            $this->reflectionClass()->getProperties(ReflectionProperty::IS_PUBLIC)
        );
    }

    private function validatePropertyType(string $propertyName, $propertyValue)
    {
        $property = $this->reflectionClass()->getProperty($propertyName);
        $type = $property->getType();
        
        if ($type) {
            $typeName = $type->getName();
            $allowsNull = $type->allowsNull();

            if ($propertyValue === null && $allowsNull) { // Se o valor for null e é permitido, retorna sem erros.
                return;
            }

            if ($typeName === 'int' && !is_int($propertyValue)) {
                $formattedValue = strstr($propertyValue, '.', true) ?: $propertyValue;

                if ($propertyValue == $formattedValue) { // Verifica se o valor original é um int
                    if (is_numeric($propertyValue)) {
                        $propertyValue = (int)$propertyValue;
                    } else {
                        throw new Exception("Invalid type for property: $propertyName. Expected int, got " . gettype($propertyValue));
                    }
                } else {
                    throw new Exception("Invalid type for property: $propertyName. Expected int, got " . gettype((float)$propertyValue));
                }

            } elseif ($typeName === 'string' && !is_string($propertyValue)) {
                throw new Exception("Invalid type for property: $propertyName. Expected string, got " . gettype($propertyValue));

            } elseif ($typeName === 'float' && !is_float($propertyValue)) {
                if (!is_numeric($propertyValue)) {
                    throw new Exception("Invalid type for property: $propertyName. Expected float, got " . gettype($propertyValue));
                }

            } elseif ($typeName === 'bool' && !is_bool($propertyValue)) {
                $booleanValue = filter_var($propertyValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                if ($booleanValue == null) {
                    throw new Exception("Invalid type for property: $propertyName. Expected bool, got " . gettype($booleanValue));                
                }
            }
        }
    }

    private function reflectionClass(): ReflectionClass
    {
        return new ReflectionClass(static::class);
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}