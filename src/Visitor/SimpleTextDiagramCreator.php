<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Visitor;

use Hirokinoue\AstVisualizer\NodeRelation;
use Hirokinoue\AstVisualizer\NodeRelations;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

final class SimpleTextDiagramCreator extends NodeVisitorAbstract
{
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
        $prefix = '';
        $layer = $node->getAttribute('layer');
        if ($layer >= 1) {
            echo $prefix = str_repeat(' ', ($node->getAttribute('layer') - 1) * 4);
        }
        if ($parentNodeRelation === null) {
            echo '';
        } else {
            echo $parentNodeRelation->isLast() ? ' └─ ' : ' ├─ ';
        }
        echo $node->getType() . PHP_EOL;

        // 次の階層の描画
        // $prefix = '';
        // $layer = $node->getAttribute('layer');
        // if ($layer >= 1) {
        //     echo $prefix = str_repeat(' ', ($node->getAttribute('layer') - 1) * 4);
        // }
        // 子ノードが存在する、かつ、$parentNodeRelation->isLast()がfalse
        // $subNodes = array_filter($node->getSubNodeNames(), function (string $name) use ($node) {
        //     return $node->$name instanceof Node;
        // });
        // if (count($subNodes) > 0 && $parentNodeRelation !== null && !$parentNodeRelation->isLast()) {
        //     echo $prefix . ' |  ';
        // }

        // $subNodes = array_filter($node->getSubNodeNames(), function (string $name) use ($node) {
        //     return $node->$name instanceof Node;
        // });
        // if (count($subNodes) !== 0) {
        //     $layer = $node->getAttribute('layer');
        //     if ($parentNodeRelation === null || !$parentNodeRelation->isLast()) {
        //         echo str_repeat(' ', $layer * 4);
        //         echo ' |  ';
        //     } else {
        //         echo str_repeat(' ', $layer * 4);
        //     }
        // }


        // 今トラバース中のノードの関連性を生成する
        $nodeRelation = new NodeRelation($node);
        NodeRelations::set($nodeRelation);
    }
}