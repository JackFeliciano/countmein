<?php

declare(strict_types=1);

namespace Countmein\Home;

/**
 * A signal is one person saying they would come. It holds the day it arrived, so
 * the pool can show when the threshold broke and in what order people stood.
 */
final class Signal
{
    public function __construct(
        public readonly string $person,
        public readonly string $day,
    ) {
        if (trim($person) === '') {
            throw new \RuntimeException('a signal carries a person');
        }
    }
}
