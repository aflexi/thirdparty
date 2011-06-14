<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
abstract class Twig_Resource
{
    protected $env;
    protected $cache;

    public function __construct(Twig_Environment $env)
    {
        $this->env = $env;
        $this->cache = array();
    }

    public function getEnvironment()
    {
        return $this->env;
    }

    protected function resolveMissingFilter($name)
    {
        throw new Twig_RuntimeError(sprintf('The filter "%s" does not exist', $name));
    }

    protected function getContext($context, $item)
    {
        if (isset($context[$item])) {
            return $context[$item];
        }

        if (!$this->env->isStrictVariables()) {
            return null;
        }

        throw new InvalidArgumentException(sprintf('Item "%s" from context does not exist.', $item));
    }

    protected function getAttribute($object, $item, array $arguments = array(), $arrayOnly = false)
    {
        $item = (string) $item;

        if ((is_array($object) || is_object($object) && $object instanceof ArrayAccess) && isset($object[$item])) {
            return $object[$item];
        }

        if ($arrayOnly || !is_object($object)) {
            if (!$this->env->isStrictVariables()) {
                return null;
            }

            throw new InvalidArgumentException(sprintf('Key "%s" for array "%s" does not exist.', $item, $object));
        }

        if (isset($object->$item)) {
            if ($this->env->hasExtension('sandbox')) {
                $this->env->getExtension('sandbox')->checkPropertyAllowed($object, $item);
            }

            return $object->$item;
        }

        $class = get_class($object);

        if (!isset($this->cache[$class])) {
            $r = new ReflectionClass($class);
            $this->cache[$class] = array();
            foreach ($r->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_FINAL) as $method) {
                $this->cache[$class][strtolower($method->getName())] = true;
            }
        }

        $item = strtolower($item);
        if (isset($this->cache[$class][$item])) {
            $method = $item;
        } elseif (isset($this->cache[$class]['get'.$item])) {
            $method = 'get'.$item;
        } elseif (isset($this->cache[$class]['__call'])) {
            $method = $item;
        } else {
            if (!$this->env->isStrictVariables()) {
                return null;
            }

            throw new InvalidArgumentException(sprintf('Method "%s" for object "%s" does not exist.', $item, get_class($object)));
        }

        if ($this->env->hasExtension('sandbox')) {
            $this->env->getExtension('sandbox')->checkMethodAllowed($object, $method);
        }

        return call_user_func_array(array($object, $method), $arguments);
    }
}
