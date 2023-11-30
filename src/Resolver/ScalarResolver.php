<?php

declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Resolver;

use PhpParser\Node;
use PhpParser\Node\Scalar\Int_;

class ScalarResolver implements Resolver
{
    public function resolve(Node $node): string
    {
        if ($node instanceof Int_) {
            return (string)$node->value;
        }
        return '';
    }
}