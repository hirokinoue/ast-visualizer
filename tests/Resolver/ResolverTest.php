<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Tests\Resolver;

use Hirokinoue\AstVisualizer\Resolver\NameResolver;
use Hirokinoue\AstVisualizer\Resolver\ScalarResolver;
use Hirokinoue\AstVisualizer\Resolver\Resolver;
use Hirokinoue\AstVisualizer\Resolver\VariableResolver;
use Hirokinoue\AstVisualizer\Tests\ResolverTestVisitor;
use PHPUnit\Framework\TestCase;
use PhpParser\NodeTraverser;
use PhpParser\Node\Expr\Variable;
use PhpParser\Parser;
use PhpParser\ParserFactory;

final class ResolverTest extends TestCase
{
    private Parser $parser;
    private NodeTraverser $traverser;

    protected function setUp(): void
    {
        $this->parser = (new ParserFactory)->createForNewestSupportedVersion();
        $this->traverser = new NodeTraverser();
    }

    /**
     * @dataProvider dataResolverがノードに応じた情報を導出できること
     * @noinspection NonAsciiCharacters
     */
    public function testResolverがノードに応じた情報を導出できること(Resolver $resolver, string $code, string $expected)
    {
        // given
        $testVisitor = new ResolverTestVisitor($resolver);
        $ast = $this->parser->parse($code);
        $this->traverser->addVisitor($testVisitor);

        // when
        $this->traverser->traverse($ast);

        // then
        $this->assertArrayHasKey($expected, $testVisitor->result, 'actual: ' . print_r($testVisitor->result, true));
        $this->assertEquals(1, $testVisitor->result[$expected]);
    }

    /**
     * @noinspection NonAsciiCharacters
     */
    public function dataResolverがノードに応じた情報を導出できること(): array
    {
        return array_map(function (array $testCase): array {
            array_walk($testCase, function (&$value, $key) {
                if ($key === 'code') {
                    $value = '<?php ' . $value . ' ;';
                }
            });
            return $testCase;
        }, [
            'Nameノード' => [
                'resolver' => new NameResolver(),
                'code' => 'new Foo()',
                'expected' => 'Foo'
            ],
            'Identifierノード' => [
                'resolver' => new NameResolver(),
                'code' => 'class Bar {}',
                'expected' => 'Bar'
            ],
            'DNumberノード' => [
                'resolver' => new ScalarResolver(),
                'code' => '1.1',
                'expected' => '1.1'
            ],
            'Float_ノード' => [
                'resolver' => new ScalarResolver(),
                'code' => '1.1',
                'expected' => '1.1'
            ],
            'LNumberノード' => [
                'resolver' => new ScalarResolver(),
                'code' => '1',
                'expected' => '1'
            ],
            'Int_ノード' => [
                'resolver' => new ScalarResolver(),
                'code' => '1',
                'expected' => '1'
            ],
            'InterpolatedStringノード' => [
                'resolver' => new ScalarResolver(),
                'code' => '"foo"',
                'expected' => 'foo'
            ],
            'String_ノード' => [
                'resolver' => new ScalarResolver(),
                'code' => '"foo"',
                'expected' => 'foo'
            ],
            'Variableノードで変数名がNameノード' => [
                'resolver' => new VariableResolver(),
                'code' => '$foo',
                'expected' => 'foo'
            ],
        ]);
    }

    /**
     * @noinspection NonAsciiCharacters
     */
    public function test変数名が式でもエラーにならないこと()
    {
        // given
        $variableNode = new Variable(new Variable('foo'));
        $resolver = new VariableResolver();

        // when
        $actual = $resolver->resolve($variableNode);

        // then
        $this->assertEquals('', $actual);
    }
}
