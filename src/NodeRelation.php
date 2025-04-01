<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer;

use PhpParser\Node;

final class NodeRelation
{
    private Node $node;

    /**
     * @param Node[]
     */
    private array $subNodes = [];

    /**
     * @param string[]
     */
    private array $drawnSubNodes = [];

    public function __construct(Node $node)
    {
        $this->node = $node;
        $this->subNodes = array_map(fn (string $name) => $node->$name, $node->getSubNodeNames());
    }

    public function nodeId(): int
    {
        return spl_object_id($this->node);
    }

    public function addDrwaSubNode(Node $node): void
    {
        $this->drawnSubNodes[] = $node;
    }

    public function isLast(): bool
    {
        return count($this->drawnSubNodes) === count($this->subNodes);
    }
}
