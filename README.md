# CountMeIn

CountMeIn is a demonstration of a platform for events that do not exist yet. Anyone can propose an event by naming the act, organiser, city, month and year, maximum ticket price and some extra information. They also set the number of signals the proposal needs before professionals can organise it.

Other people can signal a proposal. Each signal represents one person willing to pay the stated maximum price. The proposal tracks how many signals it has and how many it still needs.

## How to run the tests

CountMeIn uses plain PHP 8.3 and requires no Composer or external packages. The project includes a hand-written test runner in the `tests` directory. Run the test runner with PHP 8.3 to check the project:

```bash
php tests/run.php
```

## How to run the demo

Start the built-in server from the repository root and open the demo in your browser:

```bash
php -S 127.0.0.1:8102 -t showroom showroom/index.php
```

The demo includes three sample proposals that show the different stages of the process: open, pooled and on sale. The demo uses / as its default URL prefix. Set COUNTMEIN_BASE when a proxy passes a different URL prefix to the application.

## How a proposal moves through the stages

A proposal starts open while people signal their interest. An adviser reads the act, city and price cap, then estimates how many tickets the cheapest realistic bill would need. The organiser considers that advice and sets the threshold.

When signals reach the threshold, professionals can bid for roles in the event, including organiser, venue, manager, act, sound and transport. Each party can bid once for each role. A bid names the party, role, requested sum and offer.

The organiser selects the bids and closes the pool into a ticket sale. Every signal becomes one ticket, and the ticket price cannot exceed the original maximum.

## Limits of the demo

CountMeIn is a demonstration, not a complete event booking service. It stores nothing beyond the sample data included in the request. It does not provide persistent records of proposals, signals, bids or ticket sales. The three sample proposals illustrate the process without representing a live event marketplace.
