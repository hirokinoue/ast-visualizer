<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Tests\Annotator;

use Hirokinoue\AstVisualizer\Annotator\Annotator;
use Hirokinoue\AstVisualizer\Tests\TestVisitor;
use PHPUnit\Framework\TestCase;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;

final class AnnotatorTest extends TestCase
{
    private Parser $parser;
    private NodeTraverser $traverser;

    protected function setUp(): void
    {
        $this->parser = (new ParserFactory)->createForNewestSupportedVersion();
        $this->traverser = new NodeTraverser();
    }

    /**
     * @dataProvider dataBinaryOpCode
     * @noinspection NonAsciiCharacters
     */
    public function testBinaryOpのアノテーションがつけられること(string $code, string $expected)
    {
        // given
        $annotator = new Annotator();
        $testVisitor = new TestVisitor($annotator);
        $ast = $this->parser->parse($code);
        $this->traverser->addVisitor($testVisitor);

        // when
        $this->traverser->traverse($ast);

        // then
        $this->assertArrayHasKey($expected, $testVisitor->result);
        $this->assertEquals(1, $testVisitor->result[$expected]);
    }

    public function dataBinaryOpCode(): array
    {
        return array_map(function (array $testCase): array {
            array_walk($testCase, function (&$value, $key) {
                if ($key === 'code') {
                    $value = '<?php ' . $value . ' ;';
                }
            });
            return $testCase;
        }, [
            'BitwiseAnd' => [
                'code' => '$a & $b',
                'expected' => '&'
            ],
            'BitwiseOr' => [
                'code' => '$a | $b',
                'expected' => '|'
            ],
            'BitwiseXor' => [
                'code' => '$a ^ $b',
                'expected' => '^'
            ],
            'BooleanAnd' => [
                'code' => '$a && $b',
                'expected' => '&&'
            ],
            'BooleanOr' => [
                'code' => '$a || $b',
                'expected' => '||'
            ],
            'Coalesce' => [
                'code' => '$a ?? $b',
                'expected' => '??'
            ],
            'Concat' => [
                'code' => '$a . $b',
                'expected' => '.'
            ],
            'Div' => [
                'code' => '$a / $b',
                'expected' => '/'
            ],
            'Equal' => [
                'code' => '$a == $b',
                'expected' => '=='
            ],
            'Greater' => [
                'code' => '$a > $b',
                'expected' => '>'
            ],
            'GreaterOrEqual' => [
                'code' => '$a >= $b',
                'expected' => '>='
            ],
            'Identical' => [
                'code' => '$a === $b',
                'expected' => '==='
            ],
            'LogicalAnd' => [
                'code' => '$a and $b',
                'expected' => 'and'
            ],
            'LogicalOr' => [
                'code' => '$a or $b',
                'expected' => 'or'
            ],
            'LogicalXor' => [
                'code' => '$a xor $b',
                'expected' => 'xor'
            ],
            'Minus' => [
                'code' => '$a - $b',
                'expected' => '-'
            ],
            'Mod' => [
                'code' => '$a % $b',
                'expected' => '%'
            ],
            'Mul' => [
                'code' => '$a * $b',
                'expected' => '*'
            ],
            'NotEqual' => [
                'code' => '$a != $b',
                'expected' => '!='
            ],
            'NotIdentical' => [
                'code' => '$a !== $b',
                'expected' => '!=='
            ],
            'Plus' => [
                'code' => '$a + $b',
                'expected' => '+'
            ],
            'Pow' => [
                'code' => '$a ** $b',
                'expected' => '**'
            ],
            'ShiftLeft' => [
                'code' => '$a << $b',
                'expected' => '<<'
            ],
            'ShiftRight' => [
                'code' => '$a >> $b',
                'expected' => '>>'
            ],
            'Smaller' => [
                'code' => '$a < $b',
                'expected' => '<'
            ],
            'SmallerOrEqual' => [
                'code' => '$a <= $b',
                'expected' => '<='
            ],
            'Spaceship' => [
                'code' => '$a <=> $b',
                'expected' => '<=>'
            ],
        ]);
    }
}
