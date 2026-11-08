<?php

declare(strict_types=1);

use Countmein\Home\Proposal;
use Countmein\Home\Sample;
use Countmein\Tests\Assert;

return [
    ['the sample holds three proposals', static function (): void {
        Assert::same(3, (new Sample())->count());
    }],
    ['the sample holds ten signals', static function (): void {
        Assert::same(10, (new Sample())->signals());
    }],
    ['two proposals stand open and one stands pooled', static function (): void {
        $sample = new Sample();
        Assert::same(1, $sample->pooled());
        $stages = array_map(static fn ($pool): string => $pool->proposal->stage(), $sample->pools());
        Assert::same(['open', 'pooled', 'on_sale'], $stages);
    }],
    ['the sample holds four bids and one sale of two tickets', static function (): void {
        $sample = new Sample();
        Assert::same(4, $sample->bids());
        Assert::same(1, count($sample->sales()));
        Assert::same(2, $sample->sold());
    }],
    ['the adviser calls thirty signals for a concert steep', static function (): void {
        Assert::same(false, (new Sample())->adviser()->affordable(30));
    }],
    ['the first proposal waits on one more signal', static function (): void {
        Assert::same(1, (new Sample())->pools()[0]->left());
        Assert::same(Proposal::OPEN, (new Sample())->pools()[0]->proposal->stage());
    }],
];
