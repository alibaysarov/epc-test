<?php
declare(strict_types=1);

namespace App\DI;

use App\Exception\ContainerException;
use App\Service\NotificationService;
use App\Service\NotificationServiceInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionUnionType;

class Container implements ContainerInterface
{
    private array $entries = [];

    /**
     * @throws \ReflectionException
     * @throws ContainerException
     */
    public function get(string $id)
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];
            if (is_callable($entry)) {
                return $entry($this);
            }
            $id = $entry;
        }
        return $this->resolve($id);

    }

    /**
     * @throws \ReflectionException
     * @throws ContainerException
     */
    private function resolve(string $id): object
    {
        $reflectionClass = new ReflectionClass($id);
        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException("$id is not instantiable");
        }

        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }

        $parameters = $constructor->getParameters();
        if (!$parameters) {
            return $reflectionClass->newInstance();
        }

        $dependencies = $this->resolveDependencies($id, $parameters);

        return $reflectionClass->newInstanceArgs($dependencies);
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function set(string $id, string|callable $concrete): void
    {
        $this->entries[$id] = $concrete;
    }

    /**
     * @param string $id
     * @param array $parameters
     * @return array|null[]|object[]|string[]
     * @throws ContainerException
     * @throws \ReflectionException
     */
    private function resolveDependencies(string $id, array $parameters): array
    {
        return array_map(function (\ReflectionParameter $parameter) use ($id) {
            $name = $parameter->getName();
            $type = $parameter->getType();
            if ($type === null) {
                throw new ContainerException("Failed to resolve $id class, param {$name} is not type hinted");
            }
            if ($type instanceof ReflectionUnionType) {
                throw new ContainerException("Failed to resolve $id class,of union type for param {$name}");
            }
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                return $this->get($type->getName());
            }
            throw new ContainerException("Failed to resolve $id class, param {$name} is invalid");
        }, $parameters);
    }

//    private function bindAbstracts(): void
//    {
//        foreach ($this->bindings as $abstract => $concrete) {
//            $this->set($abstract, fn(Container $c) => $c->get($concrete));
//        }
//    }

}