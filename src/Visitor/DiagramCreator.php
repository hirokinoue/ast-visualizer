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
