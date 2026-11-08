<?php

declare(strict_types=1);

namespace Countmein\App;

use Countmein\Home\Pool;
use Countmein\Home\Sample;
use Countmein\Home\Sale;

/**
 * The HTML. Every string that reaches the page goes through h(), so an act name
 * with an ampersand in it cannot break the markup.
 */
final class Views
{
    public function __construct(private readonly string $base = '/')
    {
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function money(int $cents): string
    {
        return number_format($cents / 100, 2, '.', ',');
    }

    public function header(string $title): string
    {
        $base = $this->h($this->base);
        $t = $this->h($title);

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$t}</title>
<base href="{$base}">
<link rel="stylesheet" href="assets/site.css">
</head>
<body>
<main>
HTML;
    }

    public function footer(): string
    {
        return "</main>\n</body>\n</html>\n";
    }

    public function page(Sample $sample): string
    {
        $TITLE = 'CountMeIn, count me in';
        $LEDE = 'CountMeIn turns an idea for an event into a crowd, and a crowd into a night out.';
        $WHAT = 'CountMeIn is a signalling and bidding platform for events that do not exist yet. One person proposes a concert, a party, a talk or a trip, and names the act, the organizer, the city, the month and a maximum ticket price. Others signal CountMeIn. The signals are people prepared to pay that maximum, so they measure whether the crowd is worth bringing. Each proposal carries a threshold, which the organizer sets. When the signals reach it, professionals bid into the pool, offering the venue, the production, the sound or the artist. The organizer picks the bids, they agree on a ticket price at or below the maximum, and the signals become ticket holders. The demo shows three proposals at the start, in the pool and at the sale.';

        $out = $this->header($TITLE);
        $out .= '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h($WHAT) . '</p>' . "\n";
        $out .= '<p class="stage">Proposals: ' . $sample->count() . '. Signals: ' . $sample->signals()
            . '. Bids: ' . $sample->bids() . '. Tickets sold: ' . $sample->sold() . '.</p>' . "\n";
        $out .= '<div class="proposals">' . "\n";
        foreach ($sample->pools() as $pool) {
            $out .= $this->card($pool);
        }
        $out .= '</div>' . "\n";
        $out .= $this->adviser($sample);
        $out .= '<h2>The steps a proposal takes</h2>' . "\n";
        $out .= '<ol class="steps">' . "\n";
        foreach ([
            ['A person proposes an event', 'The proposal names the act, the organizer, the city, the month and a maximum ticket price, and sets the number of signals it needs.'],
            ['People signal CountMeIn', 'Each signal is one person prepared to pay that maximum. The pool shows the count and how many signals the threshold still wants.'],
            ['The adviser sizes the bill', 'A note reads the act, the city and the price cap, and estimates how many tickets the cheapest realistic bill would need.'],
            ['Professionals bid into the pool', 'When the signals reach the threshold, venues, organizers, managers and the artist team offer their part at a price.'],
            ['The pool becomes a ticket sale', 'The organizer accepts bids and sets a ticket price at or below the maximum. Every signal becomes one ticket.'],
        ] as [$head, $body]) {
            $out .= '<li><strong>' . $this->h($head) . '</strong> ' . $this->h($body) . '</li>' . "\n";
        }
        $out .= '</ol>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    public function card(Pool $pool): string
    {
        $proposal = $pool->proposal;
        $out = '<article class="list">' . "\n";
        $out .= '<h2>' . $this->h($proposal->title) . '</h2>' . "\n";
        $out .= '<p class="where">' . $this->h($proposal->city) . ', ' . $this->h($proposal->monthLabel())
            . '. Organized by ' . $this->h($proposal->organizer) . '. Act: ' . $this->h($proposal->act) . '.</p>' . "\n";
        $out .= '<p class="host">Maximum price: ' . $this->h($this->money($proposal->maxPriceCents))
            . '. Threshold: ' . $proposal->threshold . ' signals. Stage: ' . $this->h(str_replace('_', ' ', $proposal->stage())) . '.</p>' . "\n";
        $out .= '<dl class="counts">';
        $out .= '<dt>Signals</dt><dd>' . $pool->count() . ' of ' . max($proposal->threshold, $pool->count()) . '</dd>';
        $out .= '<dt>Still wanted</dt><dd>' . $pool->left() . '</dd>';
        $out .= '<dt>Bids</dt><dd>' . count($pool->bids()) . '</dd>';
        $out .= '<dt>Bid total</dt><dd>' . $this->h($this->money($pool->bidTotal())) . '</dd>';
        $out .= '</dl>' . "\n";
        if ($pool->bids() !== []) {
            $out .= '<table class="bids">' . "\n";
            $out .= '<tr><th>Party</th><th>Role</th><th>Offer</th><th>Asks</th></tr>' . "\n";
            foreach ($pool->bids() as $bid) {
                $out .= '<tr><td>' . $this->h($bid->party) . '</td><td>' . $this->h($bid->role) . '</td><td>'
                    . $this->h($bid->offer) . '</td><td>' . $this->h($this->money($bid->amountCents)) . '</td></tr>' . "\n";
            }
            $out .= '</table>' . "\n";
        }
        $out .= '</article>' . "\n";

        return $out;
    }

    public function adviser(Sample $sample): string
    {
        $verdict = $sample->adviser();
        $out = '<h2>The adviser reads a threshold</h2>' . "\n";
        $out .= '<p class="verdict">' . $this->h($verdict->note('Guns N Roses', 'Lisbon', 30)) . '</p>' . "\n";
        $out .= '<p class="verdict">' . $this->h($verdict->note('Guns N Roses', 'Lisbon', 1500)) . '</p>' . "\n";

        return $out;
    }

    public function missing(): string
    {
        $out = $this->header('Not found');
        $out .= '<h1>Not found</h1>' . "\n";
        $out .= '<p>That page is not here.</p>' . "\n";
        $out .= $this->footer();

        return $out;
    }
}
