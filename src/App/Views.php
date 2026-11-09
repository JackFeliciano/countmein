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
        $TITLE = 'CountMeIn, interest before the event';
        $LEDE = 'CountMeIn helps people turn proposed events into ticket sales by gathering enough paying interest before professionals organise them.';
        $WHAT = 'CountMeIn lets anyone propose an event by naming the act, organiser, city, date and maximum ticket price, then setting a signal threshold. Other people pledge their interest at that price, while an adviser estimates the audience needed to cover the cheapest realistic costs. Once enough people signal, professionals bid to organise the event and sell tickets.';

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
        $out .= '<h2>How CountMeIn works</h2>' . "\n";
        $out .= '<ol class="steps">' . "\n";
        foreach ([
            ['Propose the event', 'Propose an event with its details, maximum ticket price and the number of signals it needs.'],
            ['Signal and let the adviser read it', 'Signal your interest at that price while the adviser estimates the audience needed to make the event viable.'],
            ['Take the bids and open the sale', 'Review professional bids, select the parties and open ticket sales, with each signal becoming one ticket.'],
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
