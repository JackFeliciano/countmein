<?php

declare(strict_types=1);

use Countmein\Home\Verdict;
use Countmein\Tests\Assert;

return [
    ['the adviser names the tickets a bill needs', static function (): void {
        $verdict = new Verdict('concert', 6000000, 9000000, 12000);
        Assert::same(1250, $verdict->breakEven());
        Assert::same(false, $verdict->affordable(30));
        Assert::same(true, $verdict->affordable(1500));
    }],
    ['the note says which way a threshold looks', static function (): void {
        $verdict = new Verdict('concert', 6000000, 9000000, 12000);
        Assert::contains('look steep', $verdict->note('Guns N Roses', 'Lisbon', 30));
        Assert::contains('About 1250 would carry it', $verdict->note('Guns N Roses', 'Lisbon', 30));
        Assert::contains('cover a concert in Lisbon with Guns N Roses', $verdict->note('Guns N Roses', 'Lisbon', 1500));
    }],
    ['the adviser knows the types it reads', static function (): void {
        Assert::throws(static fn () => new Verdict('rally', 100, 100, 100), 'unknown type: rally');
    }],
    ['a verdict reads a whole price and a whole fee', static function (): void {
        Assert::throws(static fn () => new Verdict('party', 0, 100, 100), 'act fee and a venue floor');
        Assert::throws(static fn () => new Verdict('party', 100, 100, 0), 'maximum price above zero');
    }],
];
