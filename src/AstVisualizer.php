<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer;

use Hirokinoue\AstVisualizer\Traverser\DrawingTraverser;
use PhpParser\Error;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\ParserFactory;

class AstVisualizer
{
    private Parser $parser;

    /**
     * @var NodeVisitorAbstract[]
     */
    private array $formats;

    /**
     * @param NodeVisitorAbstract[] $formats
     */
    public function __construct(array $formats)
    {
        $this->parser = (new ParserFactory)->createForNewestSupportedVersion();
        $this->formats = $formats;
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
            foreach ($this->formats as $diagramFormat) {
                $nodeTraverser->addVisitor($diagramFormat);
            }
            $nodeTraverser->traverse($ast);

            return true;
        } catch (Error $error) {
            fwrite(STDERR, 'Parse error: {$error->getMessage()}' . PHP_EOL);
            return false;
        }
    }
}
