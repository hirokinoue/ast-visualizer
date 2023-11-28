# AST Visualizer
ASTを図示するツール

# 開発環境
## 起動
```
make run
```

## イメージ、コンテナの削除
```
make clean
```

## 解析実行
```
XDEBUG_MODE=off php bin/ast-visualizer path/to/Foo.php
```

## デバッグ
Xdebugを有効化して解析を実行する。
```
php bin/ast-visualizer path/to/Foo.php
```

## PHPStan
### 解析実行
```
XDEBUG_MODE=off vendor/bin/phpstan analyze -l 9 src/
```

### ベースライン生成
```angular2html
XDEBUG_MODE=off vendor/bin/phpstan analyze -l 9 src/ --generate-baseline
```

## PHPUnit
```
XDEBUG_MODE=off vendor/bin/phpunit --filter="FooTest"
```
