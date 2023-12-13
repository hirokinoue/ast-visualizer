<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer;

use PhpParser\Node\Stmt;

final class RootNode extends Stmt
{
    public function getSubNodeNames(): array {
        return [];
    }

    public function getType(): string {
        return 'Root';
    }
}
