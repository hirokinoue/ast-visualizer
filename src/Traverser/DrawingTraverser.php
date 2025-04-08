<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Traverser;

use Hirokinoue\AstVisualizer\DrawnNodes;
use Hirokinoue\AstVisualizer\Layer;
use Hirokinoue\AstVisualizer\RootNode;
use PhpParser\Node;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitor;

final class DrawingTraverser implements NodeTraverserInterface {
    /** 
     * @var list<NodeVisitor>
     */
    private array $visitors = [];

    public function __construct(NodeVisitor ...$visitors) {
        $this->visitors = $visitors;
    }

    public function addVisitor(NodeVisitor $visitor): void {
        $this->visitors[] = $visitor;
    }

    public function removeVisitor(NodeVisitor $visitor): void {
        $index = array_search($visitor, $this->visitors);
        if ($index !== false) {
            array_splice($this->visitors, $index, 1, []);
        }
    }

    /**
     * @param list<Node> $nodes
     * @return list<Node>
     */
    public function traverse(array $nodes): array {
        foreach ($this->visitors as $visitor) {
            if (null !== $return = $visitor->beforeTraverse($nodes)) {
                $nodes = $return;
            }
        }

        $nodes = $this->traverseArray($nodes, new RootNode(), (new Layer(1)));

        for ($i = \count($this->visitors) - 1; $i >= 0; --$i) {
            $visitor = $this->visitors[$i];
            if (null !== $return = $visitor->afterTraverse($nodes)) {
                $nodes = $return;
            }
        }

        return $nodes;
    }

    private function traverseNode(Node $node, Layer $layer): void {
        foreach ($node->getSubNodeNames() as $name) {
            $subNode = $node->$name;

            if (\is_array($subNode)) {
                $node->$name = $this->traverseArray($subNode, $node, $layer);
            } elseif ($subNode instanceof Node) {
                $this->setObjectNameSourceToAttribute($node, $subNode);
                $visitorIndex = -1;

                foreach ($this->visitors as $visitorIndex => $visitor) {
                    $subNode->setAttribute('layer', $layer->value);
                    $visitor->enterNode($subNode);
                    DrawnNodes::add($subNode);
                }

                $this->traverseNode($subNode, $layer->next());

                for (; $visitorIndex >= 0; --$visitorIndex) {
                    $visitor = $this->visitors[$visitorIndex];
                    $visitor->leaveNode($subNode);
                }
            }
        }
    }

    /**
     * @param list<Node> $nodes
     * @return list<Node>
     */
    private function traverseArray(array $nodes, Node $srcNode, Layer $layer): array {
        foreach ($nodes as $i => $node) {
            if ($node instanceof Node) {
                $this->setObjectNameSourceToAttribute($srcNode, $node);
                $visitorIndex = -1;

                foreach ($this->visitors as $visitorIndex => $visitor) {
                    $node->setAttribute('layer', $layer->value);
                    $visitor->enterNode($node);
                    DrawnNodes::add($node);
                }

                $this->traverseNode($node, $layer->next());

                for (; $visitorIndex >= 0; --$visitorIndex) {
                    $visitor = $this->visitors[$visitorIndex];
                    $visitor->leaveNode($node);
                }
            } elseif (\is_array($node)) {
                throw new \LogicException('Invalid node structure: Contains nested arrays');
            }
        }
        return $nodes;
    }

    private function setObjectNameSourceToAttribute(Node $parent, Node $child): void {
        $child->setAttribute('parentNodeId', spl_object_id($parent));
        $child->setAttribute('parentNodeType', $parent->getType());
        $child->setAttribute('parentSuffix', $parent->getAttribute('suffix', ''));
        $child->setAttribute('suffix', DrawnNodes::numberOfOccurrences($child));
    }
}
