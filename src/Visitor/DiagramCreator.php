<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class DiagramCreator extends NodeVisitorAbstract
{
    private Node $srcNode;

    private int $layer;

    public function __construct()
    {
    }

    public function beforeTraverse(array $nodes)
    {
        return $nodes;
    }

    public function afterTraverse(array $nodes)
    {
        return $nodes;
    }

    public function enterNode(Node $node) {
        fwrite(STDOUT, str_repeat('_', ($this->layer - 1) * 2) . $this->srcNode->getType() . PHP_EOL);
        fwrite(STDOUT, str_repeat('_', ($this->layer - 1) * 2) . $node->getType() . PHP_EOL);
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
}
