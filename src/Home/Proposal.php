<?php

declare(strict_types=1);

namespace Countmein\Home;

/**
 * A proposal is an event somebody wants to happen: an act, an organizer, a city,
 * a month, a ceiling on the ticket price and a threshold of signals that would
 * bring the professionals in.
 */
final class Proposal
{
    public const OPEN = 'open';
    public const POOLED = 'pooled';
    public const ON_SALE = 'on_sale';

    public const STAGES = [self::OPEN, self::POOLED, self::ON_SALE];

    /** @param list<string> $notes */
    public function __construct(
        public readonly string $handle,
        public readonly string $title,
        public readonly string $organizer,
        public readonly string $city,
        public readonly string $act,
        public readonly string $month,
        public readonly int $maxPriceCents,
        public readonly int $threshold,
        private array $notes = [],
        private string $stage = self::OPEN,
    ) {
        if (!preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])$/', $month)) {
            throw new \RuntimeException('a month reads as 2027-05');
        }
        if ($maxPriceCents < 1) {
            throw new \RuntimeException('a proposal carries a maximum price above zero');
        }
        if ($threshold < 1) {
            throw new \RuntimeException('a threshold asks for at least one signal');
        }
    }

    /** @return list<string> */
    public function notes(): array
    {
        return $this->notes;
    }

    public function stage(): string
    {
        return $this->stage;
    }

    public function stageTo(string $stage): void
    {
        if (!in_array($stage, self::STAGES, true)) {
            throw new \RuntimeException('unknown stage: ' . $stage);
        }
        $this->stage = $stage;
    }

    public function monthLabel(): string
    {
        $names = ['January', 'February', 'March', 'April', 'May', 'June', 'July',
            'August', 'September', 'October', 'November', 'December'];
        [$year, $month] = explode('-', $this->month);

        return $names[(int) $month - 1] . ' ' . $year;
    }
}
