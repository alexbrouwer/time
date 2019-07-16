<?php

namespace PARTest\Time\PHPStan;

use PAR\Time\Temporal\TemporalAmount;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;

class TemporalAmountDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return TemporalAmount::class;
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        if (count($methodCall->args) === 0) {
            return ParametersAcceptorSelector::selectFromArgs(
                $scope,
                $methodCall->args,
                $methodReflection->getVariants()
            )->getReturnType();
        }
        $arg = $methodCall->args[0]->value;

        return $scope->getType($arg);
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $methodName = $methodReflection->getName();

        return in_array($methodName, ['addTo', 'subtractFrom'], true);
    }
}
