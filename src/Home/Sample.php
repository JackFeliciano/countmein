<?php

declare(strict_types=1);

namespace Countmein\Home;

/**
 * The proposals the demo shows. Every figure on the page comes out of these
 * objects, so the page cannot claim a signal the code does not hold.
 */
final class Sample
{
    /** @var list<Pool> */
    private array $pools = [];

    /** @var list<Sale> */
    private array $sales = [];

    public function __construct()
    {
        $talk = new Proposal('cm-101', 'Jordan Peterson, Milan 2027', 'Mara Lind', 'Milan', 'Jordan Peterson', '2027-05', 4500, 3, [], Proposal::OPEN);
        $pool = new Pool($talk);
        $pool->signal('Ada Rune', '2026-11-02');
        $pool->signal('Miro Fell', '2026-11-02');
        $this->pools[] = $pool;

        $concert = new Proposal('cm-102', 'Guns n Roses, Lisbon 2027', 'Bright Field', 'Lisbon', 'Guns N Roses', '2027-07', 12000, 5, [], Proposal::OPEN);
        $big = new Pool($concert);
        $big->signal('Ines Barros', '2026-11-01');
        $big->signal('Jules Kader', '2026-11-01');
        $big->signal('Nadia Fenn', '2026-11-03');
        $big->signal('Owen Pike', '2026-11-04');
        $big->signal('Piet Visser', '2026-11-05');
        $big->signal('Rita Kaur', '2026-11-06');
        $big->bid(new Bid('Hall Nighters', 'venue', 9000000, 'The hall for one night, with the sound desk.'));
        $big->bid(new Bid('Kader Booking', 'organizer', 2500000, 'Production, licensing and the door.'));
        $this->pools[] = $big;

        $party = new Proposal('cm-103', 'Bairro party, Porto 2027', 'Kestrel Works', 'Porto', 'a rooftop set', '2027-03', 2500, 2, [], Proposal::OPEN);
        $small = new Pool($party);
        $small->signal('Hana Oduya', '2026-11-06');
        $small->signal('Tomas Vidal', '2026-11-06');
        $small->bid(new Bid('Rua das Flores 14', 'venue', 40000, 'The rooftop for a night, forty people.'));
        $small->bid(new Bid('Vidal Sound', 'sound', 15000, 'A small rig and one engineer.'));
        $this->pools[] = $small;
        $sale = new Sale($small, 2000);
        $sale->sell();
        $this->sales[] = $sale;
    }

    /** @return list<Pool> */
    public function pools(): array
    {
        return $this->pools;
    }

    public function count(): int
    {
        return count($this->pools);
    }

    public function signals(): int
    {
        $sum = 0;
        foreach ($this->pools as $pool) {
            $sum += $pool->count();
        }

        return $sum;
    }

    public function pooled(): int
    {
        $sum = 0;
        foreach ($this->pools as $pool) {
            if ($pool->proposal->stage() === Proposal::POOLED) {
                $sum++;
            }
        }

        return $sum;
    }

    public function bids(): int
    {
        $sum = 0;
        foreach ($this->pools as $pool) {
            $sum += count($pool->bids());
        }

        return $sum;
    }

    public function sold(): int
    {
        $sum = 0;
        foreach ($this->sales as $sale) {
            $sum += $sale->sold();
        }

        return $sum;
    }

    /** @return list<Sale> */
    public function sales(): array
    {
        return $this->sales;
    }

    public function adviser(): Verdict
    {
        return new Verdict('concert', 6000000, 9000000, 12000);
    }
}
