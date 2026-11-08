<?php

declare(strict_types=1);

use Countmein\Home\Bid;
use Countmein\Home\Pool;
use Countmein\Home\Proposal;
use Countmein\Tests\Assert;

function cm_talk(int $threshold = 3): Pool
{
    return new Pool(new Proposal('cm-1', 'Jordan Peterson, Milan 2027', 'Mara Lind', 'Milan', 'Jordan Peterson', '2027-05', 4500, $threshold));
}

return [
    ['the pool counts its signals and what the threshold still wants', static function (): void {
        $pool = cm_talk(3);
        $pool->signal('Ada Rune', '2026-11-02');
        Assert::same(1, $pool->count());
        Assert::same(2, $pool->left());
        Assert::same(false, $pool->reached());
        $pool->signal('Miro Fell', '2026-11-02');
        $pool->signal('Sefa Oyelaran', '2026-11-03');
        Assert::same(0, $pool->left());
        Assert::same(true, $pool->reached());
    }],
    ['the proposal enters its pool the moment the threshold breaks', static function (): void {
        $pool = cm_talk(2);
        Assert::same('open', $pool->proposal->stage());
        $pool->signal('Ada Rune', '2026-11-02');
        Assert::same('open', $pool->proposal->stage());
        $pool->signal('Miro Fell', '2026-11-02');
        Assert::same('pooled', $pool->proposal->stage());
    }],
    ['one person signals once', static function (): void {
        $pool = cm_talk(3);
        $pool->signal('Ada Rune', '2026-11-02');
        Assert::throws(static fn () => $pool->signal('Ada Rune', '2026-11-03'), 'already signalled');
        Assert::same(1, $pool->count());
    }],
    ['a signal carries a person', static function (): void {
        Assert::throws(static fn () => cm_talk()->signal(' ', '2026-11-02'), 'carries a person');
    }],
    ['a bid waits for the threshold', static function (): void {
        $pool = cm_talk(3);
        $pool->signal('Ada Rune', '2026-11-02');
        Assert::throws(static fn () => $pool->bid(new Bid('Hall Nighters', 'venue', 100000, 'The hall.')), 'the pool opens at 3 signals, and it holds 1');
        $pool->signal('Miro Fell', '2026-11-02');
        $pool->signal('Sefa Oyelaran', '2026-11-03');
        $pool->bid(new Bid('Hall Nighters', 'venue', 100000, 'The hall.'));
        Assert::same(1, count($pool->bids()));
    }],
    ['one party bids once for one role', static function (): void {
        $pool = cm_talk(1);
        $pool->signal('Ada Rune', '2026-11-02');
        $pool->bid(new Bid('Hall Nighters', 'venue', 100000, 'The hall.'));
        Assert::throws(static fn () => $pool->bid(new Bid('Hall Nighters', 'venue', 90000, 'The same hall again.')), 'already bid as venue');
        $pool->bid(new Bid('Hall Nighters', 'organizer', 50000, 'Production and the door.'));
    }],
    ['a bid names a known role and a sum above zero', static function (): void {
        Assert::throws(static fn () => new Bid('Hall Nighters', 'catering', 100000, 'Food.'), 'unknown role: catering');
        Assert::throws(static fn () => new Bid('Hall Nighters', 'venue', 0, 'The hall.'), 'sum above zero');
        Assert::throws(static fn () => new Bid('Hall Nighters', 'venue', 100000, ' '), 'carries an offer');
    }],
    ['the bid total and the price each signal would carry', static function (): void {
        $pool = cm_talk(2);
        $pool->signal('Ada Rune', '2026-11-02');
        $pool->signal('Miro Fell', '2026-11-02');
        $pool->bid(new Bid('Hall Nighters', 'venue', 300000, 'The hall.'));
        $pool->bid(new Bid('Kader Booking', 'organizer', 100000, 'The door.'));
        Assert::same(400000, $pool->bidTotal());
        Assert::same(200000, $pool->priceEach());
    }],
];
