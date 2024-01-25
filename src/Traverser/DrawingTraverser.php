<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Traverser;

use Hirokinoue\AstVisualizer\DrawnNodes;
use Hirokinoue\AstVisualizer\Layer;
use Hirokinoue\AstVisualizer\RootNode;
use PhpParser\Node;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitor;

final class DrawingTraverser implements NodeTraverserInterface {
    /** @var list<NodeVisitor> $visitors */
    private array $visitors = [];

    /** @var bool Whether traversal should be stopped */
    private bool $stopTraversal;

    /**
     * Create a traverser with the given visitors.
     *
     * @param NodeVisitor ...$visitors Node visitors
     */
    public function __construct(NodeVisitor ...$visitors) {
        $this->visitors = $visitors;
    }

    /**
     * Adds a visitor.
     *
     * @param NodeVisitor $visitor Visitor to add
     */
    public function addVisitor(NodeVisitor $visitor): void {
        $this->visitors[] = $visitor;
    }

    /**
     * Removes an added visitor.
     */
    public function removeVisitor(NodeVisitor $visitor): void {
        $index = array_search($visitor, $this->visitors);
        if ($index !== false) {
            array_splice($this->visitors, $index, 1, []);
        }
    }

    /**
     * Traverses an array of nodes using the registered visitors.
     *
     * @param Node[] $nodes Array of nodes
     *
     * @return Node[] Traversed array of nodes
     */
    public function traverse(array $nodes): array {
        $this->stopTraversal = false;

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

    /**
     * Recursively traverse a node.
     *
     * @param Node $node Node to traverse.
     */
    private function traverseNode(Node $node, Layer $layer): void {
        foreach ($node->getSubNodeNames() as $name) {
            $subNode = $node->$name;

            if (\is_array($subNode)) {
                $node->$name = $this->traverseArray($subNode, $node, $layer);
                if ($this->stopTraversal) {
                    break;
                }
            } elseif ($subNode instanceof Node) {
                $this->setObjectNameSourceToAttribute($node, $subNode);
                $traverseChildren = true;
                $visitorIndex = -1;

                foreach ($this->visitors as $visitorIndex => $visitor) {
                    if (method_exists($visitor, 'setLayer')) {
                        $visitor->setLayer($layer->value);
                    }
                    if (method_exists($visitor, 'setSrcNode')) {
                        $visitor->setSrcNode($node);
                    }
                    $return = $visitor->enterNode($subNode);
                    DrawnNodes::add($subNode);
                    if (null !== $return) {
                        if ($return instanceof Node) {
                            $this->ensureReplacementReasonable($subNode, $return);
                            $subNode = $node->$name = $return;
                        } elseif (NodeVisitor::DONT_TRAVERSE_CHILDREN === $return) {
                            $traverseChildren = false;
                        } elseif (NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN === $return) {
                            $traverseChildren = false;
                            break;
                        } elseif (NodeVisitor::STOP_TRAVERSAL === $return) {
                            $this->stopTraversal = true;
                            break 2;
                        } elseif (NodeVisitor::REPLACE_WITH_NULL === $return) {
                            $node->$name = null;
                            continue 2;
                        } else {
                            throw new \LogicException(
                                'enterNode() returned invalid value of type ' . gettype($return)
                            );
                        }
                    }
                }

                if ($traverseChildren) {
                    $this->traverseNode($subNode, $layer->next());
                    if ($this->stopTraversal) {
                        break;
                    }
                }

                for (; $visitorIndex >= 0; --$visitorIndex) {
                    $visitor = $this->visitors[$visitorIndex];
                    $return = $visitor->leaveNode($subNode);

                    if (null !== $return) {
                        if ($return instanceof Node) {
                            $this->ensureReplacementReasonable($subNode, $return);
                            $subNode = $node->$name = $return;
                        } elseif (NodeVisitor::STOP_TRAVERSAL === $return) {
                            $this->stopTraversal = true;
                            break 2;
                        } elseif (NodeVisitor::REPLACE_WITH_NULL === $return) {
                            $node->$name = null;
                            break;
                        } elseif (\is_array($return)) {
                            throw new \LogicException(
                                'leaveNode() may only return an array ' .
                                'if the parent structure is an array'
                            );
                        } else {
                            throw new \LogicException(
                                'leaveNode() returned invalid value of type ' . gettype($return)
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Recursively traverse array (usually of nodes).
     *
     * @param list<Node> $nodes Array to traverse
     *
     * @return list<Node> Result of traversal (may be original array or changed one)
     */
    private function traverseArray(array $nodes, Node $srcNode, Layer $layer): array {
        $doNodes = [];

        foreach ($nodes as $i => $node) {
            if ($node instanceof Node) {
                $this->setObjectNameSourceToAttribute($srcNode, $node);
                $traverseChildren = true;
                $visitorIndex = -1;

                foreach ($this->visitors as $visitorIndex => $visitor) {
                    if (method_exists($visitor, 'setLayer')) {
                        $visitor->setLayer($layer->value);
                    }
                    if (method_exists($visitor, 'setSrcNode')) {
                        $visitor->setSrcNode($srcNode);
                    }
                    $return = $visitor->enterNode($node);
                    DrawnNodes::add($node);
                    if (null !== $return) {
                        if ($return instanceof Node) {
                            $this->ensureReplacementReasonable($node, $return);
                            $nodes[$i] = $node = $return;
                        } elseif (\is_array($return)) {
                            $doNodes[] = [$i, $return];
                            continue 2;
                        } elseif (NodeVisitor::REMOVE_NODE === $return) {
                            $doNodes[] = [$i, []];
                            continue 2;
                        } elseif (NodeVisitor::DONT_TRAVERSE_CHILDREN === $return) {
                            $traverseChildren = false;
                        } elseif (NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN === $return) {
                            $traverseChildren = false;
                            break;
                        } elseif (NodeVisitor::STOP_TRAVERSAL === $return) {
                            $this->stopTraversal = true;
                            break 2;
                        } elseif (NodeVisitor::REPLACE_WITH_NULL === $return) {
                            throw new \LogicException(
                                'REPLACE_WITH_NULL can not be used if the parent structure is an array');
                        } else {
                            throw new \LogicException(
                                'enterNode() returned invalid value of type ' . gettype($return)
                            );
                        }
                    }
                }

                if ($traverseChildren) {
                    $this->traverseNode($node, $layer->next());
                    if ($this->stopTraversal) {
                        break;
                    }
                }

                for (; $visitorIndex >= 0; --$visitorIndex) {
                    $visitor = $this->visitors[$visitorIndex];
                    $return = $visitor->leaveNode($node);

                    if (null !== $return) {
                        if ($return instanceof Node) {
                            $this->ensureReplacementReasonable($node, $return);
                            $nodes[$i] = $node = $return;
                        } elseif (\is_array($return)) {
                            $doNodes[] = [$i, $return];
                            break;
                        } elseif (NodeVisitor::REMOVE_NODE === $return) {
                            $doNodes[] = [$i, []];
                            break;
                        } elseif (NodeVisitor::STOP_TRAVERSAL === $return) {
                            $this->stopTraversal = true;
                            break 2;
                        } elseif (NodeVisitor::REPLACE_WITH_NULL === $return) {
                            throw new \LogicException(
                                'REPLACE_WITH_NULL can not be used if the parent structure is an array');
                        } else {
                            throw new \LogicException(
                                'leaveNode() returned invalid value of type ' . gettype($return)
                            );
                        }
                    }
                }
            } elseif (\is_array($node)) {
                throw new \LogicException('Invalid node structure: Contains nested arrays');
            }
        }

        if (!empty($doNodes)) {
            while (list($i, $replace) = array_pop($doNodes)) {
                array_splice($nodes, $i, 1, $replace);
            }
        }

        return $nodes;
    }

    private function ensureReplacementReasonable(Node $old, Node $new): void {
        if ($old instanceof Node\Stmt && $new instanceof Node\Expr) {
            throw new \LogicException(
                "Trying to replace statement ({$old->getType()}) " .
                "with expression ({$new->getType()}). Are you missing a " .
                "Stmt_Expression wrapper?"
            );
        }

        if ($old instanceof Node\Expr && $new instanceof Node\Stmt) {
            throw new \LogicException(
                "Trying to replace expression ({$old->getType()}) " .
                "with statement ({$new->getType()})"
            );
        }
    }

    private function setObjectNameSourceToAttribute(Node $parent, Node $child): void {
        $child->setAttribute('parentNodeType', $parent->getType());
        $child->setAttribute('parentSuffix', $parent->getAttribute('suffix', ''));
        $child->setAttribute('suffix', DrawnNodes::numberOfOccurrences($child));
    }
}
