<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer;

class Layer
{
    public int $value;

    public function  __construct(int $value)
    {
        $this->value = $value;
    }

    public function next(): Layer
    {
        return new self($this->value + 1);
    }
}
