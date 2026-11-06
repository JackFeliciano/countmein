<?php

declare(strict_types=1);

namespace Countmein\App;

use Countmein\Home\List_;
use Countmein\Home\Sample;

/**
 * The HTML. Every string that reaches the page goes through h(), so a guest name
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
        $TITLE = 'CountMeIn, the guest list that counts';
        $LEDE = 'CountMeIn keeps one guest list for each event. Hosts manage event details, guests record their answers, and the list tracks attendance, available seats and the waiting order.';
        $WHAT = 'The page shows a working demo of CountMeIn, with 3 sample events and 11 guests. Each event has a title, place, town, start day, end day, seat limit and visibility setting. Guests can answer going, maybe or no. CountMeIn records the day each answer arrives and counts all 3 responses. When guests confirm after seats run out, CountMeIn adds them to the wait list in reply order. The demo runs on plain PHP 8.3, without Composer or packages.';

        $out = $this->header($TITLE);
        $out .= '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h($WHAT) . '</p>' . "\n";
        $out .= '<p class="stage">Events: ' . $sample->count() . '. Guests going: ' . $sample->going() . '.</p>' . "\n";
        $out .= '<div class="lists">' . "\n";
        foreach ($sample->lists() as $list) {
            $out .= $this->card($list);
        }
        $out .= '</div>' . "\n";
        $out .= '<h2>The five steps a guest list takes</h2>' . "\n";
        $out .= '<ol class="steps">' . "\n";
        foreach ([
            ['Host opens the event', 'The host sets title, place, day and seats, and chooses whether the event is public or restricted.'],
            ['Guest replies', 'A guest answers going, maybe or no. The answer records the day it arrived.'],
            ['List counts the answers', 'The list adds the going, the maybe and the no, and sets the seats left.'],
            ['Wait list opens at capacity', 'A yes that arrives after the seats run out joins the wait list in the order it came.'],
            ['Host releases a seat', 'The first name on the wait list takes the seat, or the host cancels the event and takes no guest.'],
        ] as $i => [$step, $line]) {
            $out .= '<li><strong>' . $this->h($step) . '</strong> ' . $this->h($line) . '</li>' . "\n";
        }
        $out .= '</ol>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    private function card(List_ $list): string
    {
        $e = $list->event;
        $out = '<article class="list">' . "\n";
        $out .= '<h2>' . $this->h($e->title) . '</h2>' . "\n";
        $out .= '<p class="where">' . $this->h($e->place . ', ' . $e->town) . ' on ' . $this->h($e->startsOn) . '</p>' . "\n";
        $out .= '<p class="host">Host: ' . $this->h($e->host) . '. ' . $this->h(ucfirst($e->visibility())) . ' event.</p>' . "\n";
        $out .= '<dl class="counts">';
        $out .= '<dt>Going</dt><dd>' . $list->going() . ' of ' . $e->capacity() . '</dd>';
        $out .= '<dt>Maybe</dt><dd>' . $list->maybe() . '</dd>';
        $out .= '<dt>No</dt><dd>' . $list->no() . '</dd>';
        $out .= '<dt>Seats left</dt><dd>' . $list->seatsLeft() . '</dd>';
        $out .= '</dl>' . "\n";
        $wait = $list->waitList();
        $out .= '<p class="wait">' . ($wait === []
            ? 'No one waits for a seat.'
            : 'Waiting: ' . $this->h(implode(', ', $wait)) . '.') . '</p>' . "\n";
        $out .= '</article>' . "\n";

        return $out;
    }

    public function missing(): string
    {
        return $this->header('Not found') . "<h1>Not found</h1>\n<p>That page does not exist here.</p>\n" . $this->footer();
    }
}
