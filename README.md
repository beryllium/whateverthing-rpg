One-Page RPG
============

Inspired by a tweet relating to the current political boondoggle in Britain,
this quick PHP toy attempts to interpret one-page RPG rules into a playable
game.

Current requirements and limitations:

1. The OPRPG can only use a D6
2. There can be only one outer category of events and only one inner
   category of events; event categorization can't go deeper than that
3. There can only be one player per terminal session

Use `composer install` to get the dependencies installed,
then run `bin/start` to run the game.