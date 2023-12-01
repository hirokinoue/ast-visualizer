<?php

declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Annotator;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;

class Annotator
{
    public function annotate(Node $node): string
    {
        if ($node instanceof Assign) {
            return '=';
        }
        return '';
    }
}
