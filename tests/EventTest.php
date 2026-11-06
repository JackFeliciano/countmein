<?php

declare(strict_types=1);

use Countmein\Home\Event;
use Countmein\Tests\Assert;

return [
    ['an event seats at least one guest', static function (): void {
        Assert::throws(static fn () => new Event('e1', 'T', 'H', 'P', 'T', '2026-12-05', '2026-12-05', 0), 'at least one guest');
    }],
    ['an event ends on or after the day it starts', static function (): void {
        Assert::throws(static fn () => new Event('e1', 'T', 'H', 'P', 'T', '2026-12-06', '2026-12-05', 4), 'or after it');
    }],
    ['a public event is the default visibility', static function (): void {
        $event = new Event('e1', 'Supper', 'Mara', 'Rua 14', 'Porto', '2026-12-05', '2026-12-05', 4);
        Assert::same(Event::PUBLIC_EVENT, $event->visibility());
        Assert::same(4, $event->capacity());
    }],
    ['a cancelled event keeps the day it had', static function (): void {
        $event = new Event('e1', 'Supper', 'Mara', 'Rua 14', 'Porto', '2026-12-05', '2026-12-05', 4);
        $event->cancel();
        Assert::same(true, $event->isCancelled());
        Assert::throws(static fn () => $event->reschedule('2026-12-09', '2026-12-09'), 'keeps the day');
    }],
    ['a published event keeps its day', static function (): void {
        $event = new Event('e1', 'Supper', 'Mara', 'Rua 14', 'Porto', '2026-12-05', '2026-12-05', 4);
        $event->reschedule('2026-12-05', '2026-12-05');
        Assert::throws(static fn () => $event->reschedule('2026-12-09', '2026-12-09'), 'holds its day');
    }],
    ['a two day event counts one night', static function (): void {
        $event = new Event('e1', 'Night', 'Bright Field', 'Av 210', 'Lisbon', '2027-01-16', '2027-01-17', 120);
        Assert::same(1, $event->nights());
    }],
];
