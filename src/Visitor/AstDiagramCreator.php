<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Visitor;

use Hirokinoue\AstVisualizer\Annotator\Annotator;
use Hirokinoue\AstVisualizer\Resolver\NameResolver;
use Hirokinoue\AstVisualizer\Resolver\Resolver;
use Hirokinoue\AstVisualizer\Resolver\ScalarResolver;
use Hirokinoue\AstVisualizer\Resolver\VariableResolver;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class AstDiagramCreator extends NodeVisitorAbstract
{
    private Node $srcNode;

    private int $layer;

    /**
     * @var Resolver[] $resolvers
     */
    private array $resolvers;

    /**
     * @var array<string, int>
     */
    private array $drawnNodes = [];

    public function __construct()
    {
        $this->resolvers = [
            new NameResolver(),
            new ScalarResolver(),
            new VariableResolver(),
        ];
    }

    public function beforeTraverse(array $nodes)
    {
        fwrite(STDOUT, '@startuml' . PHP_EOL);
        fwrite(STDOUT, 'Object Root' . PHP_EOL);
        return $nodes;
    }

    public function afterTraverse(array $nodes)
    {
        fwrite(STDOUT, '@enduml' . PHP_EOL);
        return $nodes;
    }

    public function enterNode(Node $node) {
        $this->cache($node);
        $this->drawObject($node);
        $this->drawAnnotation($node);
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

    private function drawObject(Node $node): void
    {
        $value = $this->value($node);
        fwrite(STDOUT,
            'Object ' . $this->suffixedNodeType($node) . PHP_EOL .
            ($value === '' ? '' : ($this->suffixedNodeType($node) . ' : ' . $value . PHP_EOL))
        );
    }

    private function value(Node $node): string
    {
        $value = '';
        foreach ($this->resolvers as $resolver) {
            $value = $resolver->resolve($node);
            if ($value !== '') {
                break;
            }
        }
        return $value;
    }

    private function suffixedNodeType(Node $node): string
    {
        $count = array_key_exists($node->getType(), $this->drawnNodes) ? $this->drawnNodes[$node->getType()] : 1;
        return $node->getType() === 'Root' ? 'Root' : $node->getType() . '_' . $count;
    }

    private function drawDependency(Node $node): void
    {
        fwrite(STDOUT, $this->suffixedNodeType($this->srcNode) . '-->' . $this->suffixedNodeType($node) . PHP_EOL);
    }

    private function drawAnnotation(Node $node): void
    {
        $annotation = (new Annotator())->annotate($node);
        if ($annotation !== '') {
            fwrite(STDOUT, sprintf('note right of %s: %s', $this->suffixedNodeType($node), $annotation) . PHP_EOL);
        }
    }
}
