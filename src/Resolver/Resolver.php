<?php

declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Resolver;

use PhpParser\Node;

interface Resolver
{
    public function resolve(Node $node): string;
}