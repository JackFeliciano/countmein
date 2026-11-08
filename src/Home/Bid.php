<?php

declare(strict_types=1);

namespace Countmein\Home;

/**
 * A professional bids into the pool. A bid names the party, the role they bring
 * and the sum they ask for the job.
 */
final class Bid
{
    public const ROLES = ['organizer', 'venue', 'manager', 'act', 'sound', 'transport'];

    public function __construct(
        public readonly string $party,
        public readonly string $role,
        public readonly int $amountCents,
        public readonly string $offer,
    ) {
        if (!in_array($role, self::ROLES, true)) {
            throw new \RuntimeException('unknown role: ' . $role);
        }
        if ($amountCents < 1) {
            throw new \RuntimeException('a bid asks for a sum above zero');
        }
        if (trim($offer) === '') {
            throw new \RuntimeException('a bid carries an offer');
        }
    }
}
