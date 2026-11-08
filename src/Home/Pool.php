<?php

declare(strict_types=1);

namespace Countmein\Home;

/**
 * The pool of signals behind one proposal. When the count reaches the threshold
 * the pool opens and professionals may bid, and the proposal moves on.
 */
final class Pool
{
    /** @var list<Signal> */
    private array $signals = [];

    /** @var list<Bid> */
    private array $bids = [];

    public function __construct(public readonly Proposal $proposal)
    {
    }

    public function signal(string $person, string $day): Signal
    {
        foreach ($this->signals as $already) {
            if ($already->person === $person) {
                throw new \RuntimeException($person . ' already signalled this proposal');
            }
        }
        if ($this->proposal->stage() === Proposal::ON_SALE) {
            throw new \RuntimeException('a proposal on sale takes no signal');
        }
        $signal = new Signal($person, $day);
        $this->signals[] = $signal;
        if ($this->reached() && $this->proposal->stage() === Proposal::OPEN) {
            $this->proposal->stageTo(Proposal::POOLED);
        }

        return $signal;
    }

    /** @return list<Signal> */
    public function signals(): array
    {
        return $this->signals;
    }

    public function count(): int
    {
        return count($this->signals);
    }

    public function left(): int
    {
        return max(0, $this->proposal->threshold - $this->count());
    }

    public function reached(): bool
    {
        return $this->count() >= $this->proposal->threshold;
    }

    public function bid(Bid $bid): void
    {
        if (!$this->reached()) {
            throw new \RuntimeException(
                'the pool opens at ' . $this->proposal->threshold . ' signals, and it holds ' . $this->count()
            );
        }
        foreach ($this->bids as $already) {
            if ($already->party === $bid->party && $already->role === $bid->role) {
                throw new \RuntimeException($bid->party . ' already bid as ' . $bid->role);
            }
        }
        $this->bids[] = $bid;
    }

    /** @return list<Bid> */
    public function bids(): array
    {
        return $this->bids;
    }

    public function bidTotal(): int
    {
        $sum = 0;
        foreach ($this->bids as $bid) {
            $sum += $bid->amountCents;
        }

        return $sum;
    }

    /** The ticket price a signal holder would pay if the bids divided by the pool. */
    public function priceEach(): int
    {
        if ($this->count() === 0) {
            throw new \RuntimeException('no signal holds a ticket');
        }

        return intdiv($this->bidTotal(), $this->count());
    }
}
