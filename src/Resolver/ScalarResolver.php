<?php

declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Resolver;

use PhpParser\Node;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\InterpolatedStringPart;
use PhpParser\Node\Scalar\String_;

final class ScalarResolver implements Resolver
{
    public function resolve(Node $node): string
    {
        if ($node instanceof DNumber ||
            $node instanceof Float_ ||
            $node instanceof LNumber ||
            $node instanceof Int_ ||
            $node instanceof InterpolatedStringPart ||
            $node instanceof String_
        ) {
            return (string)$node->value;
        }
        return '';
    }
}
