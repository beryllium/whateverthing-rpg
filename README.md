Whateverthing: The One-Page RPG Engine
======================================

Inspired by a tweet relating to the current political boondoggle in Britain,
the Whateverthing One-Page RPG Engine attempts to interpret one-page RPG
rules into a playable game.

Current requirements and limitations:

1. The OPRPG can only use a d6
2. There can be only one outer category of events and only one inner
   category of events; event categorization can't go deeper than that
3. There can only be one player per terminal session

## Installing and Running
Use `composer install` to get the dependencies installed,
then run `bin/start` to run the game.

## Would you like to play a game?

The game will prompt for which Gamesheet you want to run. It builds this list
by scanning the "gamesheets" folder for JSON files that define game rules.

Then, the game will ask you to roll a d6 (or press enter for a random roll).

This result will be evaluated against the 'outer' events to determine which
category the event falls into. Then, the game will ask for a second d6 roll.

The second d6 roll will determine the outcome of the turn and apply the
stats adjustments to your player stats.

The cycle begins again after displaying your current stats.

Once a game-ending rule/condition is reached, the game will conclude with an
appropriate message about the final outcome.