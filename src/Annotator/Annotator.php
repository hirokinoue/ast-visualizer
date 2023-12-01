<?php

declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Annotator;

use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp\BitwiseAnd;
use PhpParser\Node\Expr\AssignOp\BitwiseOr;
use PhpParser\Node\Expr\AssignOp\BitwiseXor;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use PhpParser\Node\Expr\AssignOp\Concat;
use PhpParser\Node\Expr\AssignOp\Div;
use PhpParser\Node\Expr\AssignOp\Minus;
use PhpParser\Node\Expr\AssignOp\Mod;
use PhpParser\Node\Expr\AssignOp\Mul;
use PhpParser\Node\Expr\AssignOp\Plus;
use PhpParser\Node\Expr\AssignOp\Pow;
use PhpParser\Node\Expr\AssignOp\ShiftLeft;
use PhpParser\Node\Expr\AssignOp\ShiftRight;
use PhpParser\Node\Expr\MethodCall;

class Annotator
{
    public function annotate(Node $node): string
    {
        if ($node instanceof Assign) {
            return '=';
        }
        if ($node instanceof BitwiseAnd) {
            return '&=';
        }
        if ($node instanceof BitwiseOr) {
            return '|=';
        }
        if ($node instanceof BitwiseXor) {
            return '^=';
        }
        if ($node instanceof Coalesce) {
            return '??=';
        }
        if ($node instanceof Concat) {
            return '.=';
        }
        if ($node instanceof Div) {
            return '/=';
        }
        if ($node instanceof Minus) {
            return '-=';
        }
        if ($node instanceof Mod) {
            return '%=';
        }
        if ($node instanceof Mul) {
            return '*=';
        }
        if ($node instanceof Plus) {
            return '+=';
        }
        if ($node instanceof Pow) {
            return '**=';
        }
        if ($node instanceof ShiftLeft) {
            return '<<=';
        }
        if ($node instanceof ShiftRight) {
            return '>>=';
        }
        if ($node instanceof MethodCall) {
            return '->';
        }
        if ($node instanceof Const_) {
            return '=';
        }
        return '';
    }
}
