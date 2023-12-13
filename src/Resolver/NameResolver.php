<?php

declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Resolver;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

final class NameResolver implements Resolver
{
    public function resolve(Node $node): string
    {
        if ($node instanceof Identifier) {
            return $node->name;
        }
        if ($node instanceof Name) {
            return $node->getLast();
        }
        return '';
    }
}
