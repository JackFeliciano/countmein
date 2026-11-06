<?php

declare(strict_types=1);

namespace Countmein\Home;

/**
 * A guest answers one of three ways: going, maybe or no. The answer carries the
 * day it arrived, so a host can tell a fresh yes from one that has aged.
 */
final class Guest
{
    public const GOING = 'going';
    public const MAYBE = 'maybe';
    public const NO = 'no';

    private const ANSWERS = [self::GOING, self::MAYBE, self::NO];

    public function __construct(
        public readonly string $name,
        private string $answer = self::MAYBE,
        private ?string $answeredOn = null,
    ) {
    }

    public function answer(): string
    {
        return $this->answer;
    }

    public function answeredOn(): ?string
    {
        return $this->answeredOn;
    }

    public function reply(string $answer, string $day): void
    {
        if (!in_array($answer, self::ANSWERS, true)) {
            throw new \RuntimeException('a guest answers going, maybe or no');
        }
        $this->answer = $answer;
        $this->answeredOn = $day;
    }

    public function going(): bool
    {
        return $this->answer === self::GOING;
    }
}
