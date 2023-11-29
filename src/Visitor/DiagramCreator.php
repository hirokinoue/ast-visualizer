<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Visitor;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;
use ReflectionClass;
use ReflectionMethod;

class DiagramCreator extends NodeVisitorAbstract
{
    private Node $srcNode;

    private int $layer;

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
        fwrite(STDOUT, 'Class Root' . PHP_EOL);
        return $nodes;
    }

    public function afterTraverse(array $nodes)
    {
        fwrite(STDOUT, '@enduml' . PHP_EOL);
        return $nodes;
    }

    public function enterNode(Node $node) {
        $this->cache($node);
        $this->drawClass($node);
        $this->drawDependency($node);
        return $node;
    }

    public function setLayer(int $layer): void
    {
        $this->layer = $layer;
    }

    public function setSrcNode(Node $srcNode): void
    {
        $this->srcNode = $srcNode;
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
        $reflectionClass = new ReflectionClass($node);
        $reflectionMethods = $reflectionClass->getMethods();

        fwrite(STDOUT,
            $this->declareType($reflectionClass) . ' ' . $this->suffixedName($node) . $this->annotation($node) . PHP_EOL .
            '{' . PHP_EOL .
            implode(PHP_EOL , array_map(fn (ReflectionMethod $reflectionMethod) =>
                $this->resolveVisibility($reflectionMethod) . $reflectionMethod->getName() . '()' . ': ' . $reflectionMethod->getReturnType()
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

    private function suffixedName(Node $node): string
    {
        $count = array_key_exists($node->getType(), $this->drawnNodes) ? $this->drawnNodes[$node->getType()] : 1;
        return $node->getType() . ($count === 1 ? '' : (string)$count);
    }

    private function annotation(Node $node): string
    {
        $name = $this->resolveName($node);
        return $name === '' ? '' : ' <<' . $name . '>> ';
    }

    private function resolveName(Node $node): string
    {
        if ($node instanceof Identifier) {
            return $node->name;
        }
        if ($node instanceof Name) {
            return $node->getLast();
        }
        return '';
    }

    private function drawDependency(Node $node): void
    {
        fwrite(STDOUT, $this->suffixedName($this->srcNode) . '-->' . $this->suffixedName($node) . PHP_EOL);
    }

    private function resolveVisibility(ReflectionMethod $reflectionMethod): string
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
}
