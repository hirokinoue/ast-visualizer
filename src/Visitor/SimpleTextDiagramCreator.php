<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Visitor;

use Hirokinoue\AstVisualizer\Border;
use Hirokinoue\AstVisualizer\NodeRelation;
use Hirokinoue\AstVisualizer\NodeRelations;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

final class SimpleTextDiagramCreator extends NodeVisitorAbstract
{
    private array $previousPrefix = [];

    public function enterNode(Node $node)
    {
        // 親ノードの関連性を取得する
        $parentNodeId = $node->getAttribute('parentNodeId');
        $parentNodeRelation = NodeRelations::get($parentNodeId);

        // 親ノードの関連性に描画済み子ノードを追加する
        if ($parentNodeRelation !== null) {
            $parentNodeRelation->addDrawnSubNode($node);
        }

        // 描画する
        // 今の階層の描画
        $prefix = $this->prefix($node->getAttribute('layer'), $parentNodeRelation, $this->previousPrefix);
        echo sprintf('%s%s%s', implode('', $prefix), $node->getType(), PHP_EOL);

        // 今トラバース中のノードの関連性を生成する
        $nodeRelation = new NodeRelation($node);
        NodeRelations::set($nodeRelation);

        // プレフィックスを保存する
        $this->previousPrefix = $prefix;
    }

    private function prefix(int $layer, ?NodeRelation $nodeRelation, array $previousPrefix): array
    {
        if ($layer <= 1) {
            return [];
        }

        $result = [];
        $end = count($previousPrefix) < $layer - 2 ? count($previousPrefix) : $layer - 2;
        for ($i = 0; $i < $end; $i++) {
            switch ($previousPrefix[$i]) {
                case Border::VERTICAL->value:
                    $result[] = Border::VERTICAL->value;
                    break;
                case Border::CHILD->value:
                    $result[] = Border::VERTICAL->value;
                    break;
                default:
                    $result[] = Border::EMPTY->value;
                    break;
            }
        }

        $result[] = $nodeRelation->isLast() ? Border::LAST_CHILD->value : Border::CHILD->value;

        return $result;
    }
}