<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Tests;

use Hirokinoue\AstVisualizer\Annotator\Annotator;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class AnnotatorTestVisitor extends NodeVisitorAbstract
{
    private Annotator $annotator;
    /** @var array<string, int> */
    public array $result = [];

    public function __construct(Annotator $annotator)
    {
        $this->annotator = $annotator;
    }

    public function enterNode(Node $node): void
    {
        $annotation = $this->annotator->annotate($node);
        if (array_key_exists($annotation, $this->result)) {
            $this->result[$annotation]++;
        } else {
            $this->result[$annotation] = 1;
        }
    }
}
