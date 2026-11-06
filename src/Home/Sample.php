<?php

declare(strict_types=1);

namespace Countmein\Home;

/**
 * The events the demo shows. Every figure on the page comes out of these objects,
 * so the page cannot claim a count the code does not hold.
 */
final class Sample
{
    /** @var list<List_> */
    private array $lists = [];

    public function __construct()
    {
        $party = new Event('ev-101', 'Rooftop supper', 'Mara Lind', 'Rua das Flores 14', 'Porto', '2026-12-05', '2026-12-05', 3);
        $list = new List_($party);
        $list->add(new Guest('Ada Rune', Guest::GOING, '2026-11-02'));
        $list->add(new Guest('Miro Fell', Guest::GOING, '2026-11-02'));
        $list->add(new Guest('Sefa Oyelaran', Guest::GOING, '2026-11-03'));
        $list->add(new Guest('Tomas Vidal', Guest::GOING, '2026-11-03'));
        $list->add(new Guest('Rita Kaur', Guest::MAYBE, '2026-11-04'));
        $this->lists[] = $list;

        $concert = new Event('ev-102', 'Hall night, second set', 'Bright Field', 'Avenida Central 210', 'Lisbon', '2027-01-16', '2027-01-17', 120);
        $big = new List_($concert);
        $big->add(new Guest('Ines Barros', Guest::GOING, '2026-11-01'));
        $big->add(new Guest('Jules Kader', Guest::GOING, '2026-11-01'));
        $big->add(new Guest('Nadia Fenn', Guest::MAYBE, '2026-11-05'));
        $big->add(new Guest('Owen Pike', Guest::NO, '2026-11-05'));
        $this->lists[] = $big;

        $gather = new Event('ev-103', 'Walking weekend', 'Kestrel Works', 'Vale do Rio', 'Coimbra', '2027-03-20', '2027-03-22', 24, Event::RESTRICTED);
        $small = new List_($gather);
        $small->add(new Guest('Hana Oduya', Guest::GOING, '2026-11-06'));
        $small->add(new Guest('Piet Visser', Guest::MAYBE, '2026-11-06'));
        $this->lists[] = $small;
    }

    /** @return list<List_> */
    public function lists(): array
    {
        return $this->lists;
    }

    public function count(): int
    {
        return count($this->lists);
    }

    public function going(): int
    {
        $sum = 0;
        foreach ($this->lists as $list) {
            $sum += $list->going();
        }

        return $sum;
    }
}
