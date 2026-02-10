<?php

namespace App\Core;

/**
 * Service Locator / DI Container [#102]
 */
class Container
{
    private static $services = [];

    public static function set(string $name, $service): void
    {
        self::$services[$name] = $service;
    }

    public static function get(string $id)
    {
        // 1. Return singleton if already stored
        if (isset(self::$services[$id])) {
            return self::$services[$id];
        }

        // 2. Handle aliases/interfaces if needed (simple version)
        // For now, we assume $id is a class name or a known service key

        // 3. Lazy load core services (Aliases)
        if ($id === 'db' || $id === Database::class) {
            return self::$services[Database::class] = Database::getInstance();
        }
        if ($id === 'auth') {
             return new Auth(); // Auth is static mostly, but if requested as object
        }

        // 4. Auto-wiring via Reflection
        try {
            $reflector = new \ReflectionClass($id);

            if (!$reflector->isInstantiable()) {
                throw new \Exception("Class [$id] is not instantiable.");
            }

            $constructor = $reflector->getConstructor();

            if (is_null($constructor)) {
                return new $id;
            }

            $parameters = $constructor->getParameters();
            $dependencies = [];

            foreach ($parameters as $parameter) {
                $type = $parameter->getType();

                if (!$type || $type->isBuiltin()) {
                    // Primitive types can't be auto-resolved easily without configuration
                    if ($parameter->isDefaultValueAvailable()) {
                        $dependencies[] = $parameter->getDefaultValue();
                    } else {
                        throw new \Exception("Cannot resolve primitive parameter [{$parameter->name}] in class [$id]");
                    }
                } else {
                    // Recursive injection
                    $class = $type->getName();
                    $dependencies[] = self::get($class);
                }
            }

            return $reflector->newInstanceArgs($dependencies);

        } catch (\ReflectionException $e) {
            // Class doesn't exist or other reflection error
            return null; 
        }
    }
}
