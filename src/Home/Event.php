<?php

declare(strict_types=1);

namespace Countmein\Home;

/**
 * An event is something a host puts in a place at a time. It holds a capacity and
 * a visibility, and it opens a wait list when the going count reaches capacity.
 */
final class Event
{
    public const PUBLIC_EVENT = 'public';
    public const RESTRICTED = 'restricted';

    public function __construct(
        public readonly string $handle,
        public readonly string $title,
        public readonly string $host,
        public readonly string $place,
        public readonly string $town,
        public readonly string $startsOn,
        public readonly string $endsOn,
        private int $capacity,
        private string $visibility = self::PUBLIC_EVENT,
        private bool $cancelled = false,
    ) {
        if ($capacity < 1) {
            throw new \RuntimeException('an event seats at least one guest');
        }
        if ($startsOn > $endsOn) {
            throw new \RuntimeException('an event ends on the day it starts or after it');
        }
    }

    public function capacity(): int
    {
        return $this->capacity;
    }

    public function visibility(): string
    {
        return $this->visibility;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    public function cancel(): void
    {
        $this->cancelled = true;
    }

    public function reschedule(string $startsOn, string $endsOn): void
    {
        if ($this->cancelled) {
            throw new \RuntimeException('a cancelled event keeps the day it had');
        }
        if ($startsOn > $endsOn) {
            throw new \RuntimeException('an event ends on the day it starts or after it');
        }
        if ($startsOn === $this->startsOn) {
            return;
        }
        throw new \RuntimeException('a published event holds its day, so a change goes out as a new event');
    }

    public function nights(): int
    {
        $a = new \DateTimeImmutable($this->startsOn);
        $b = new \DateTimeImmutable($this->endsOn);

        return (int) $a->diff($b)->days;
    }
}
