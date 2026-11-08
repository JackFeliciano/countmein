<?php

declare(strict_types=1);

namespace Countmein\Home;

/**
 * What the adviser says about a proposal. It reads the act, the city, the month
 * and the maximum price, and it names the number of tickets that would carry the
 * cheapest realistic bill. It advises. The organizer still sets the threshold.
 */
final class Verdict
{
    public const TYPES = ['concert', 'show', 'party', 'talk', 'travel'];

    public function __construct(
        public readonly string $type,
        public readonly int $actFeeCents,
        public readonly int $venueFromCents,
        public readonly int $maxPriceCents,
    ) {
        if (!in_array($type, self::TYPES, true)) {
            throw new \RuntimeException('unknown type: ' . $type);
        }
        if ($actFeeCents < 1 || $venueFromCents < 1) {
            throw new \RuntimeException('the adviser needs an act fee and a venue floor');
        }
        if ($maxPriceCents < 1) {
            throw new \RuntimeException('a verdict reads a maximum price above zero');
        }
    }

    public function breakEven(): int
    {
        $cheapest = $this->actFeeCents + $this->venueFromCents;

        return (int) ceil($cheapest / $this->maxPriceCents);
    }

    public function affordable(int $threshold): bool
    {
        return $threshold >= $this->breakEven();
    }

    public function note(string $act, string $city, int $threshold): string
    {
        $need = $this->breakEven();
        if ($this->affordable($threshold)) {
            return 'At ' . $this->money($this->maxPriceCents) . ' a ticket, ' . $threshold
                . ' signals cover a ' . $this->type . ' in ' . $city . ' with ' . $act . '.';
        }

        return 'A ' . $this->type . ' with ' . $act . ' in ' . $city . ' carries a bill near '
            . $this->money($this->actFeeCents + $this->venueFromCents) . '. At '
            . $this->money($this->maxPriceCents) . ' a ticket, ' . $threshold
            . ' signals look steep. About ' . $need . ' would carry it.';
    }

    public function money(int $cents): string
    {
        return number_format($cents / 100, 2, '.', ',');
    }
}
