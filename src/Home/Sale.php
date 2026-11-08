<?php

declare(strict_types=1);

namespace Countmein\Home;

/**
 * The ticket sale that closes a proposal. Every signal holder takes one ticket at
 * the sale price, and the pool closes.
 */
final class Sale
{
    /** @var list<string> */
    private array $holders = [];

    public function __construct(
        private readonly Pool $pool,
        private readonly int $priceCents,
    ) {
        if (!$pool->reached()) {
            throw new \RuntimeException('a sale waits for the threshold');
        }
        if ($pool->bids() === []) {
            throw new \RuntimeException('a sale waits for a bid');
        }
        if ($priceCents < 1) {
            throw new \RuntimeException('a ticket carries a price above zero');
        }
        if ($priceCents > $pool->proposal->maxPriceCents) {
            throw new \RuntimeException('a ticket passes the maximum price the organizer set');
        }
        $pool->proposal->stageTo(Proposal::ON_SALE);
    }

    public function sell(): int
    {
        foreach ($this->pool->signals() as $signal) {
            $this->holders[] = $signal->person;
        }

        return $this->sold();
    }

    public function sold(): int
    {
        return count($this->holders);
    }

    public function gross(): int
    {
        return $this->sold() * $this->priceCents;
    }

    public function covers(): bool
    {
        return $this->gross() >= $this->pool->bidTotal();
    }
}
