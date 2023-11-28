<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer;

use Hirokinoue\AstVisualizer\Traverser\DrawingTraverser;
use Hirokinoue\AstVisualizer\Visitor\DiagramCreator;
use PhpParser\Error;
use PhpParser\Parser;
use PhpParser\ParserFactory;

class AstVisualizer
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory)->createForNewestSupportedVersion();
    }

    public function analyze(string $code): bool
    {
        try {
            $ast = $this->parser->parse($code);
            if ($ast === null) {
                fwrite(STDERR, 'No AST found.' . PHP_EOL);
                return false;
            }
            $nodeTraverser = new DrawingTraverser();
            $nodeTraverser->addVisitor(new DiagramCreator());
            $nodeTraverser->traverse($ast);

            return true;
        } catch (Error $error) {
            fwrite(STDERR, 'Parse error: {$error->getMessage()}' . PHP_EOL);
            return false;
        }
    }
}
