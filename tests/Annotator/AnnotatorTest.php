<?php declare(strict_types=1);

namespace Hirokinoue\AstVisualizer\Tests\Annotator;

use Hirokinoue\AstVisualizer\Annotator\Annotator;
use Hirokinoue\AstVisualizer\Tests\AnnotatorTestVisitor;
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
     * @dataProvider dataノードの種類に応じたアノテーションがつけられること
     * @noinspection NonAsciiCharacters
     */
    public function testノードの種類に応じたアノテーションがつけられること(string $code, string $expected)
    {
        // given
        $annotator = new Annotator();
        $testVisitor = new AnnotatorTestVisitor($annotator);
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
    public function dataノードの種類に応じたアノテーションがつけられること(): array
    {
        return array_map(function (array $testCase): array {
            array_walk($testCase, function (&$value, $key) {
                if ($key === 'code') {
                    $value = '<?php ' . $value . ' ;';
                }
            });
            return $testCase;
        }, [
            'Assign' => [
                'code' => '$a = $b',
                'expected' => '='
            ],
            'AssignBitwiseAnd' => [
                'code' => '$a &= $b',
                'expected' => '&='
            ],
            'AssignBitwiseOr' => [
                'code' => '$a |= $b',
                'expected' => '|='
            ],
            'AssignBitwiseXor' => [
                'code' => '$a ^= $b',
                'expected' => '^='
            ],
            'AssignCoalesce' => [
                'code' => '$a ??= $b',
                'expected' => '??='
            ],
            'AssignConcat' => [
                'code' => '$a .= $b',
                'expected' => '.='
            ],
            'AssignDiv' => [
                'code' => '$a /= $b',
                'expected' => '/='
            ],
            'AssignMinus' => [
                'code' => '$a -= $b',
                'expected' => '-='
            ],
            'AssignMod' => [
                'code' => '$a %= $b',
                'expected' => '%='
            ],
            'AssignMul' => [
                'code' => '$a *= $b',
                'expected' => '*='
            ],
            'AssignPlus' => [
                'code' => '$a += $b',
                'expected' => '+='
            ],
            'AssignPow' => [
                'code' => '$a **= $b',
                'expected' => '**='
            ],
            'AssignShiftLeft' => [
                'code' => '$a <<= $b',
                'expected' => '<<='
            ],
            'AssignShiftRight' => [
                'code' => '$a >>= $b',
                'expected' => '>>='
            ],
            'MethodCall' => [
                'code' => '$a->b()',
                'expected' => '->'
            ],
            'PropertyFetch' => [
                'code' => '$a->b',
                'expected' => '->'
            ],
            'Const' => [
                'code' => 'const FOO = "foo"',
                'expected' => '='
            ],
            'DeclareItem' => [
                'code' => 'declare(strict_types=1);',
                'expected' => '='
            ],
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
            'ClassConstFetch' => [
                'code' => 'FOO::BAR',
                'expected' => '&#58;&#58;'
            ],
        ]);
    }
}
