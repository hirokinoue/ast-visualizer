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
        $subNodes = [];
        foreach ($node->getSubNodeNames() as $name) {
            $subNode = $node->$name;
            if ($subNode instanceof Node) {
                $subNodes[] = $subNode;
            } elseif (is_array($subNode)) {
                foreach ($subNode as $item) {
                    if ($item instanceof Node) {
                        $subNodes[] = $item;
                    }
                }
            }
        }
        $this->subNodes = $subNodes;
    }

    public function nodeId(): int
    {
        return spl_object_id($this->node);
    }

    public function addDrawnSubNode(Node $node): void
    {
        $this->drawnSubNodes[] = $node;
    }

    public function isLast(): bool
    {
        if (empty($this->subNodes)) {
            return true;
        }
        return count($this->drawnSubNodes) >= count($this->subNodes);
    }
}
