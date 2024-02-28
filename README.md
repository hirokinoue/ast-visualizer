# AST Visualizer
[![phpunit](https://github.com/hirokinoue/ast-visualizer/actions/workflows/phpunit.yml/badge.svg)](https://github.com/hirokinoue/ast-visualizer/actions/workflows/phpunit.yml)

## Overview
AST Visualizer is a tool that visualizes ast created by PHP-Parser.

# Installation
1. Edit composer.json
    ```
    {
      "repositories": [
        {
          "type": "vcs",
          "url": "https://github.com/hirokinoue/ast-visualizer"
        }
      ]
    }
    ```

2. Install using composer  
   `$ composer require --dev hirokinoue/ast-visualizer`

# Usage
- ast mode
    ```
    ./vendor/bin/ast-visualizer path/to/Foo.php ast
    ```

- node mode
    ```
    ./vendor/bin/ast-visualizer path/to/Foo.php node
    ```

Both modes can be specified at the same time.  
PHP 8.1.0 or higher is required.

# Example
```
<?php declare(strict_types=1);

echo 'The World!';
```
This code is converted to the following diagram.

- ast mode  
![ast mode sample](./ast_mode_sample.png)

- node mode  
![node mode sample](./node_mode_sample.png)
