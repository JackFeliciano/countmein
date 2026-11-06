<?php

declare(strict_types=1);

use Countmein\Home\Guest;
use Countmein\Home\Sample;
use Countmein\Tests\Assert;

return [
    ['the sample holds three events', static function (): void {
        Assert::same(3, (new Sample())->count());
    }],
    ['the sample counts seven guests going', static function (): void {
        Assert::same(7, (new Sample())->going());
    }],
    ['the first sample event runs one guest over capacity', static function (): void {
        $list = (new Sample())->lists()[0];
        Assert::same(4, $list->going());
        Assert::same(3, $list->event->capacity());
        Assert::same(['Tomas Vidal'], $list->waitList());
    }],
    ['the restricted event keeps its visibility', static function (): void {
        $list = (new Sample())->lists()[2];
        Assert::same('restricted', $list->event->visibility());
        Assert::same(1, $list->maybe());
        Assert::same(Guest::GOING, $list->guests()[0]->answer());
    }],
];
