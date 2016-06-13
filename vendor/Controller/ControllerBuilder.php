<?php

namespace Minor\Controller;

use ReflectionClass;
use ReflectionException;

class ControllerBuilder
{
    public static function buildController($controllerName, $actionName)
    {
        try {
            $controllerClass = new ReflectionClass($controllerName);

            if (($constructor = $controllerClass->getConstructor()) && $constructor->getNumberOfRequiredParameters > 0)
                throw new ControllerException('自定义控制器[' . $controllerName . ']:构造函数存在必须输入参数');

            if (!$controllerClass->hasMethod('setApp'))
                throw new ControllerException('自定义控制器[' . $controllerName . ']:未定义方法[setApp]');

            if ($controllerClass->hasMethod('setMinorRequest'))
                throw new ControllerException('自定义控制器[' . $controllerName . ']:未定义方法[setMinorRequest]');

            if ($controllerClass->hasMethod('setMinorResponse'))
                throw new ControllerException('自定义控制器[' . $controllerName . ']:未定义方法[setMinorResponse]');

            return [$controllerClass->newInstance(), self::getReflectionMethod($controllerClass, $controllerName, $actionName)];
        } catch (ReflectionException $re) {
            throw new ControllerException('自定义控制器[' . $controllerName . ']:实例化失败');
        }

        return null;
    }

    public static function getReflectionMethod(ReflectionClass $class , $className , $actionName)
    {
        if (!$class->hasMethod($actionName))
            throw new ControllerException('自定义控制器[' . $className . ']:没有定义' . $actionName . '方法');

        $method = $class->getMethod($actionName);

        if (!$method->isPublic())
            throw new ControllerException('自定义控制器[' . $className . ']:方法[' . $actionName . ']必须为public方法');

        return $method;
    }
}