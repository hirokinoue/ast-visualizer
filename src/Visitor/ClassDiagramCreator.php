<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

final class ClassDiagramCreator extends NodeVisitorAbstract
{
    /**
     * @var array<string, int>
     */
    private array $drawnNodes = [];

    public function __construct()
    {
    }

    public function beforeTraverse(array $nodes)
    {
        fwrite(STDOUT, '@startuml' . PHP_EOL);
        return $nodes;
    }

    public function afterTraverse(array $nodes)
    {
        fwrite(STDOUT, '@enduml' . PHP_EOL);
        return $nodes;
    }

    public function enterNode(Node $node): null
    {
        $this->cache($node);
        $this->drawClass($node);
        return null;
    }

    private function cache(Node $node): void
    {
        if (array_key_exists($node->getType(), $this->drawnNodes)) {
            $this->drawnNodes[$node->getType()]++;
        } else {
            $this->drawnNodes[$node->getType()] = 1;
        }
    }

    private function drawClass(Node $node): void
    {
        if (array_key_exists($node->getType(), $this->drawnNodes) && $this->drawnNodes[$node->getType()] > 1) {
            return;
        }
        $reflectionClass = new ReflectionClass($node);
        $reflectionProperties = $reflectionClass->getProperties();
        $reflectionMethods = $reflectionClass->getMethods();

        fwrite(STDOUT,
            $this->declareType($reflectionClass) . ' ' . $node->getType() . PHP_EOL .
            '{' . PHP_EOL .
            implode(PHP_EOL , array_map(fn (ReflectionProperty $reflectionProperty) =>
                    $this->resolveVisibilityOfProperty($reflectionProperty) . $reflectionProperty->getName() . ': ' . $reflectionProperty->getType()
                    , $reflectionProperties)
            ) . PHP_EOL .
            implode(PHP_EOL , array_map(fn (ReflectionMethod $reflectionMethod) =>
                    $this->resolveVisibilityOfMethod($reflectionMethod) . $reflectionMethod->getName() . '()' . ': ' . $reflectionMethod->getReturnType()
                    , $reflectionMethods)
            ) . PHP_EOL .
            '}' . PHP_EOL);
    }

    private function declareType(ReflectionClass $reflectionClass): string
    {
        if ($reflectionClass->isInterface()) {
            return 'interface';
        }
        if ($reflectionClass->isTrait()) {
            return 'trait';
        }
        if ($reflectionClass->isAbstract()) {
            return 'abstract class';
        }
        if ($reflectionClass->isEnum()) {
            return 'enum';
        }
        return 'class';
    }

    private function resolveVisibilityOfMethod(ReflectionMethod $reflectionMethod): string
    {
        if ($reflectionMethod->isPublic()) {
            return '+';
        }
        if ($reflectionMethod->isProtected()) {
            return '#';
        }
        if ($reflectionMethod->isPrivate()) {
            return '-';
        }
        return '';
    }

    private function resolveVisibilityOfProperty(ReflectionProperty $reflectionProperty): string
    {
        if ($reflectionProperty->isPublic()) {
            return '+';
        }
        if ($reflectionProperty->isProtected()) {
            return '#';
        }
        if ($reflectionProperty->isPrivate()) {
            return '-';
        }
        return '';
    }
//    private function annotation(Node $node): string
//    {
//        $name = $this->resolveName($node);
//        return $name === '' ? '' : ' <<' . $name . '>> ';
//    }
}
