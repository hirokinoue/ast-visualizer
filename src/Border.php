<?php

declare(strict_types=1);

namespace Hirokinoue\AstVisualizer;

enum Border: string
{
    case EMPTY = '    ';
    case LAST_CHILD = ' └─ ';
    case CHILD = ' ├─ ';
    case VERTICAL = ' │  ';
}