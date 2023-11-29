<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

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
}
