<?php
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        SetList::PRIVATIZATION,
    ]);
};
