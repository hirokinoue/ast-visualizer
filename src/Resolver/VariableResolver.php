<?php

declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Resolver;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;

class VariableResolver implements Resolver
{
    public function resolve(Node $node): string
    {
        if ($node instanceof Variable && is_string($node->name)) {
            return $node->name;
        }
        return '';
    }
}
