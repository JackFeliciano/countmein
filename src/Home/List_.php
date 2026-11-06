<?php

declare(strict_types=1);

namespace Countmein\Home;

/**
 * A guest list holds the guests of one event and turns their answers into counts.
 * The waiting list keeps the order guests replied, so a host releases seats in the
 * order people asked for them.
 */
final class List_
{
    /** @param list<Guest> $guests */
    public function __construct(
        public readonly Event $event,
        private array $guests = [],
    ) {
    }

    public function add(Guest $guest): void
    {
        if ($this->event->isCancelled()) {
            throw new \RuntimeException('a cancelled event takes no guest');
        }
        foreach ($this->guests as $held) {
            if ($held->name === $guest->name) {
                throw new \RuntimeException('a guest joins one list once');
            }
        }
        $this->guests[] = $guest;
    }

    /** @return list<Guest> */
    public function guests(): array
    {
        return $this->guests;
    }

    public function going(): int
    {
        return count(array_filter($this->guests, static fn (Guest $g): bool => $g->going()));
    }

    public function maybe(): int
    {
        return count(array_filter($this->guests, static fn (Guest $g): bool => $g->answer() === Guest::MAYBE));
    }

    public function no(): int
    {
        return count(array_filter($this->guests, static fn (Guest $g): bool => $g->answer() === Guest::NO));
    }

    public function seatsLeft(): int
    {
        return max(0, $this->event->capacity() - $this->going());
    }

    public function full(): bool
    {
        return $this->going() >= $this->event->capacity();
    }

    /**
     * The wait list holds the guests who said yes after the seats ran out, in the
     * order they replied, so the first name takes the first released seat.
     *
     * @return list<string>
     */
    public function waitList(): array
    {
        $seated = 0;
        $waiting = [];
        foreach ($this->guests as $guest) {
            if (!$guest->going()) {
                continue;
            }
            if ($seated < $this->event->capacity()) {
                $seated++;
                continue;
            }
            $waiting[] = $guest->name;
        }

        return $waiting;
    }

    public function release(): ?string
    {
        $waiting = $this->waitList();

        return $waiting === [] ? null : $waiting[0];
    }
}
