<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Tests;

use Hirokinoue\AstVisualizer\Resolver\Resolver;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ResolverTestVisitor extends NodeVisitorAbstract
{
    private Resolver $resolver;
    /** @var array<string, int> */
    public array $result = [];

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function enterNode(Node $node): void
    {
        $resolved = $this->resolver->resolve($node);
        if (array_key_exists($resolved, $this->result)) {
            $this->result[$resolved]++;
        } else {
            $this->result[$resolved] = 1;
        }
    }
}
