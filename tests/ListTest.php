<?php

declare(strict_types=1);

use Countmein\Home\Event;
use Countmein\Home\Guest;
use Countmein\Home\List_;
use Countmein\Tests\Assert;

function cmin_party(int $seats = 3): List_
{
    return new List_(new Event('ev-101', 'Rooftop supper', 'Mara Lind', 'Rua das Flores 14', 'Porto', '2026-12-05', '2026-12-05', $seats));
}

return [
    ['a guest answers going, maybe or no', static function (): void {
        $guest = new Guest('Ada Rune');
        Assert::same('maybe', $guest->answer());
        $guest->reply('going', '2026-11-02');
        Assert::same('going', $guest->answer());
        Assert::same('2026-11-02', $guest->answeredOn());
        Assert::throws(static fn () => $guest->reply('perhaps', '2026-11-02'), 'going, maybe or no');
    }],
    ['the list counts the three answers', static function (): void {
        $list = cmin_party();
        $list->add(new Guest('Ada Rune', Guest::GOING, '2026-11-02'));
        $list->add(new Guest('Miro Fell', Guest::GOING, '2026-11-02'));
        $list->add(new Guest('Rita Kaur', Guest::MAYBE, '2026-11-04'));
        $list->add(new Guest('Owen Pike', Guest::NO, '2026-11-05'));
        Assert::same(2, $list->going());
        Assert::same(1, $list->maybe());
        Assert::same(1, $list->no());
        Assert::same(1, $list->seatsLeft());
        Assert::same(false, $list->full());
    }],
    ['a guest joins one list once', static function (): void {
        $list = cmin_party();
        $list->add(new Guest('Ada Rune', Guest::GOING, '2026-11-02'));
        Assert::throws(static fn () => $list->add(new Guest('Ada Rune', Guest::GOING, '2026-11-03')), 'one list once');
    }],
    ['a cancelled event takes no guest', static function (): void {
        $list = cmin_party();
        $list->event->cancel();
        Assert::throws(static fn () => $list->add(new Guest('Ada Rune', Guest::GOING, '2026-11-02')), 'takes no guest');
    }],
    ['the wait list keeps the order of the replies', static function (): void {
        $list = cmin_party(2);
        $list->add(new Guest('Ada Rune', Guest::GOING, '2026-11-02'));
        $list->add(new Guest('Miro Fell', Guest::GOING, '2026-11-02'));
        $list->add(new Guest('Sefa Oyelaran', Guest::GOING, '2026-11-03'));
        $list->add(new Guest('Tomas Vidal', Guest::GOING, '2026-11-04'));
        Assert::same(true, $list->full());
        Assert::same(4, $list->going());
        Assert::same(0, $list->seatsLeft());
        Assert::same(['Sefa Oyelaran', 'Tomas Vidal'], $list->waitList());
        Assert::same('Sefa Oyelaran', $list->release());
    }],
    ['an empty wait list releases nobody', static function (): void {
        $list = cmin_party(3);
        $list->add(new Guest('Ada Rune', Guest::GOING, '2026-11-02'));
        Assert::same([], $list->waitList());
        Assert::same(null, $list->release());
    }],
    ['a maybe holds no seat', static function (): void {
        $list = cmin_party(1);
        $list->add(new Guest('Rita Kaur', Guest::MAYBE, '2026-11-04'));
        Assert::same(0, $list->going());
        Assert::same(1, $list->seatsLeft());
        Assert::same(false, $list->full());
    }],
];
