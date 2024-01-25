<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer;

use PhpParser\Node;

final class DrawnNodes
{
    /**
     * @var array<string, int>
     */
    private static array $drawnNodes = [];

    public static function add(Node $node): void
    {
        if (array_key_exists($node->getType(), self::$drawnNodes)) {
            self::$drawnNodes[$node->getType()]++;
        } else {
            self::$drawnNodes[$node->getType()] = 1;
        }
    }

    public static function numberOfOccurrences(Node $node): string
    {
        $key = $node->getType();
        if (array_key_exists($key, self::$drawnNodes)) {
            return (string)(self::$drawnNodes[$key] + 1);
        }
        return '1';
    }
}
