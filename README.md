# CountMeIn

## What the project is

that keeps 1 guest list per event. Hosts set event details, including the title, place, town, start day, end day, seat limit and visibility. Guests answer going, maybe or no. CountMeIn records the day each answer arrives and counts all 3 responses.

The guest list tracks available seats. When a yes arrives after the seats run out, CountMeIn adds that guest to the wait list. The wait list keeps the order of replies, so the first name takes the first released seat.

The repository includes a working demo with 3 sample events and 11 guests. The first event has 3 seats and 4 guests who said going. One guest therefore waits for a seat.

The programme uses plain PHP 8.3. It requires no Composer and no packages.

## How to run it

Run the test suite from the repository root:

```bash
php tests/run.php
```


Start the demo with PHP's built-in server:

```bash
php -S 127.0.0.1:8102 -t showroom showroom/index.php
```


The demo uses / as its default URL prefix. Set COUNTMEIN_BASE when a proxy passes a different URL prefix to the application.

For example, set the environment variable to the prefix that the proxy passes. The application uses this value when it builds its routes.

## What the routes answer

The demo exposes 2 routes.

/ renders the 3 sample events, their guest counts and the 5 steps a guest list takes.
/health answers with the event count and the number of guests who said going.

The sample data gives visitors a view of event attendance and seat allocation. The first event shows the wait list in action, with 4 going replies for 3 available seats.

## How the layout is arranged

The repository separates event data, application handling, the demo entry point, tests and deployment files.

src/Home/ holds Event, Guest, List_ and Sample.
src/App/ holds the router, front controller and HTML.
showroom/ holds the entry file and stylesheet.
tests/ holds the test suite.
deploy/ holds the systemd unit and nginx location block.

Each directory has a defined role in the application. The layout keeps the event and guest list code separate from routing, presentation and deployment configuration.

## The state of the suite

The repository includes a hand-written test runner with 17 checks.

Run all checks with:

```bash
php tests/run.php
```


The runner uses no package installation or external test framework. It provides a direct way to check the programme from the command line.

## Licence

CountMeIn carries the Mozilla Public License 2.0.

See the repository's licence file for the full licence terms.

