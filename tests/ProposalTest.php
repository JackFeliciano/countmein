<?php

declare(strict_types=1);

use Countmein\Home\Proposal;
use Countmein\Tests\Assert;

return [
    ['a proposal reads its month', static function (): void {
        $proposal = new Proposal('cm-1', 'Talk', 'Mara Lind', 'Milan', 'a speaker', '2027-05', 4500, 3);
        Assert::same('May 2027', $proposal->monthLabel());
        Assert::same('open', $proposal->stage());
    }],
    ['a month outside the calendar is refused', static function (): void {
        Assert::throws(static fn () => new Proposal('cm-1', 'Talk', 'Mara', 'Milan', 'a speaker', '2027-13', 4500, 3), 'reads as 2027-05');
        Assert::throws(static fn () => new Proposal('cm-1', 'Talk', 'Mara', 'Milan', 'a speaker', 'May', 4500, 3), 'reads as 2027-05');
    }],
    ['a proposal carries a price and a threshold above zero', static function (): void {
        Assert::throws(static fn () => new Proposal('cm-1', 'Talk', 'Mara', 'Milan', 'a speaker', '2027-05', 0, 3), 'maximum price above zero');
        Assert::throws(static fn () => new Proposal('cm-1', 'Talk', 'Mara', 'Milan', 'a speaker', '2027-05', 4500, 0), 'at least one signal');
    }],
    ['a stage outside the list is refused', static function (): void {
        $proposal = new Proposal('cm-1', 'Talk', 'Mara', 'Milan', 'a speaker', '2027-05', 4500, 3);
        Assert::throws(static fn () => $proposal->stageTo('launched'), 'unknown stage: launched');
    }],
];
