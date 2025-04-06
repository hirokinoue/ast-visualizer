<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer;

final class NodeRelations
{
    /**
     * @var list<NodeRelation>
     */
    private static array $nodeRelations = [];

    public static function set(NodeRelation $nodeRelation): void
    {
        self::$nodeRelations[$nodeRelation->nodeId()] = $nodeRelation;
    }

    public static function get(int $nodeId): ?NodeRelation
    {
        return self::$nodeRelations[$nodeId] ?? null;
    }
}
