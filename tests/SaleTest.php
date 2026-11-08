<?php

declare(strict_types=1);

use Countmein\Home\Bid;
use Countmein\Home\Pool;
use Countmein\Home\Proposal;
use Countmein\Home\Sale;
use Countmein\Tests\Assert;

function cm_pooled(): Pool
{
    $pool = new Pool(new Proposal('cm-1', 'Bairro party, Porto 2027', 'Kestrel Works', 'Porto', 'a rooftop set', '2027-03', 2500, 2));
    $pool->signal('Hana Oduya', '2026-11-06');
    $pool->signal('Tomas Vidal', '2026-11-06');
    $pool->bid(new Bid('Rua das Flores 14', 'venue', 40000, 'The rooftop for a night.'));

    return $pool;
}

return [
    ['a sale waits for the threshold', static function (): void {
        $pool = new Pool(new Proposal('cm-1', 'Party', 'Kestrel', 'Porto', 'a set', '2027-03', 2500, 2));
        $pool->signal('Hana Oduya', '2026-11-06');
        Assert::throws(static fn () => new Sale($pool, 2000), 'waits for the threshold');
    }],
    ['a sale waits for a bid', static function (): void {
        $pool = new Pool(new Proposal('cm-1', 'Party', 'Kestrel', 'Porto', 'a set', '2027-03', 2500, 2));
        $pool->signal('Hana Oduya', '2026-11-06');
        $pool->signal('Tomas Vidal', '2026-11-06');
        Assert::throws(static fn () => new Sale($pool, 2000), 'waits for a bid');
    }],
    ['a ticket passes the maximum price and no more', static function (): void {
        Assert::throws(static fn () => new Sale(cm_pooled(), 2600), 'passes the maximum price');
        Assert::throws(static fn () => new Sale(cm_pooled(), 0), 'price above zero');
    }],
    ['every signal holder takes one ticket', static function (): void {
        $pool = cm_pooled();
        $sale = new Sale($pool, 2000);
        Assert::same('on_sale', $pool->proposal->stage());
        Assert::same(2, $sale->sell());
        Assert::same(4000, $sale->gross());
        Assert::same(false, $sale->covers());
    }],
    ['a sale at a price near the cap leaves a shortfall', static function (): void {
        $pool = cm_pooled();
        $sale = new Sale($pool, 2500);
        $sale->sell();
        Assert::same(5000, $sale->gross());
        Assert::same(false, $sale->covers());
    }],
    ['a sale that covers the bid total', static function (): void {
        $pool = new Pool(new Proposal('cm-2', 'Party', 'Kestrel', 'Porto', 'a set', '2027-03', 2500, 2));
        $pool->signal('Hana Oduya', '2026-11-06');
        $pool->signal('Tomas Vidal', '2026-11-06');
        $pool->bid(new Bid('Vidal Sound', 'sound', 1500, 'A small rig.'));
        $sale = new Sale($pool, 1000);
        $sale->sell();
        Assert::same(2000, $sale->gross());
        Assert::same(true, $sale->covers());
    }],
    ['a proposal on sale takes no new signal', static function (): void {
        $pool = cm_pooled();
        (new Sale($pool, 2000))->sell();
        Assert::throws(static fn () => $pool->signal('Rita Kaur', '2026-11-07'), 'takes no signal');
    }],
];
