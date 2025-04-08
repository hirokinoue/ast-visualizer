<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Visitor;

use Hirokinoue\AstVisualizer\Annotator\Annotator;
use Hirokinoue\AstVisualizer\Resolver\NameResolver;
use Hirokinoue\AstVisualizer\Resolver\Resolver;
use Hirokinoue\AstVisualizer\Resolver\ScalarResolver;
use Hirokinoue\AstVisualizer\Resolver\VariableResolver;
use Monolog\Logger;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use InvalidArgumentException;

final class AstDiagramCreator extends NodeVisitorAbstract
{
    /**
     * @var Resolver[] $resolvers
     */
    private array $resolvers;
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->resolvers = [
            new NameResolver(),
            new ScalarResolver(),
            new VariableResolver(),
        ];
        $this->logger = $logger;
    }

    public function beforeTraverse(array $nodes)
    {
        fwrite(STDOUT, '@startuml' . PHP_EOL);
        fwrite(STDOUT, 'Object Root' . PHP_EOL);
        return $nodes;
    }

    public function afterTraverse(array $nodes)
    {
        fwrite(STDOUT, '@enduml' . PHP_EOL);
        return $nodes;
    }

    public function enterNode(Node $node): null
    {
        $this->logger->info('Layer: ' . $node->getAttribute('layer', '') . '. ' . $node->getType() . ' is drawn.');
        $this->drawObject($node);
        $this->drawAnnotation($node);
        $this->drawDependency($node);
        return null;
    }

    private function drawObject(Node $node): void
    {
        $value = $this->value($node);
        $object = $this->appendSuffixToNode($node, $this->getAttributeAsString($node, 'suffix'));
        fwrite(STDOUT,
            'Object ' . $object . PHP_EOL .
            (preg_replace('/[ 　]/', '', $value) === '' ? '' : $object . ' : ' . $value . PHP_EOL)
        );
    }

    private function value(Node $node): string
    {
        $value = '';
        foreach ($this->resolvers as $resolver) {
            $value = $resolver->resolve($node);
            if ($value !== '') {
                break;
            }
        }
        return $value;
    }

    private function appendSuffixToNode(Node $node, string $suffix = ''): string
    {
        return $this->appendSuffixToNodeType($node->getType(), $suffix);
    }

    private function appendSuffixToNodeType(string $nodeType, string $suffix = ''): string
    {
        if ($nodeType === 'Root' || $suffix === '' || $suffix === '0') {
            return $nodeType;
        }
        return $nodeType . '_' . $suffix;
    }

    private function drawDependency(Node $node): void
    {
        $parentNodeType = $this->getAttributeAsString($node, 'parentNodeType');
        $parentSuffix = $this->getAttributeAsString($node, 'parentSuffix');
        $suffix = $this->getAttributeAsString($node, 'suffix');
        fwrite(STDOUT,
            $this->appendSuffixToNodeType($parentNodeType, $parentSuffix) .
            '-->' .
            $this->appendSuffixToNode($node, $suffix) .
            PHP_EOL
        );
    }

    private function drawAnnotation(Node $node): void
    {
        $annotation = (new Annotator())->annotate($node);
        if ($annotation !== '') {
            fwrite(STDOUT,
                sprintf('note right of %s: %s',
                    $this->appendSuffixToNode($node, $this->getAttributeAsString($node, 'suffix')),
                    $annotation
                ) . PHP_EOL
            );
        }
    }

    private function getAttributeAsString(Node $node, string $key): string
    {
        $value = $node->getAttribute($key, '');
        if (!is_string($value)) {
            throw new InvalidArgumentException('Value of ' . $key . ' attribute must be string.');
        }
        return $value;
    }
}
