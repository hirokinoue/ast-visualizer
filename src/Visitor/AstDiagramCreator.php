<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Visitor;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;

class AstDiagramCreator extends NodeVisitorAbstract
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
        $objName = $this->resolveName($node);
        fwrite(STDOUT,
            'Object ' . $this->suffixedNodeType($node) . PHP_EOL .
            ($objName === '' ? '' : ($this->suffixedNodeType($node) . ' : ' . $objName . PHP_EOL))
        );
    }

    private function suffixedNodeType(Node $node): string
    {
        $count = array_key_exists($node->getType(), $this->drawnNodes) ? $this->drawnNodes[$node->getType()] : 1;
        return $node->getType() === 'Root' ? 'Root' : $node->getType() . '__' . $count;
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
        fwrite(STDOUT, $this->suffixedNodeType($this->srcNode) . '-->' . $this->suffixedNodeType($node) . PHP_EOL);
    }
}
