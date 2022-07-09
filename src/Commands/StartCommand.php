<?php

namespace Beryllium\OnePageRpg\Commands;

use Beryllium\OnePageRpg\Gamesheet;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'start')]
class StartCommand extends Command
{
    /**
     * @param QuestionHelper $questionHelper
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Question $rollQuestion
     * @return int|mixed
     * @throws \Exception
     */
    public function getRoll(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, Question $rollQuestion): mixed
    {
        $roll = $questionHelper->ask($input, $output, $rollQuestion);
        if (!$roll) {
            $roll = random_int(1, 6);
        }
        return $roll;
    }

    protected function configure()
    {
        $this->setDescription("Start the Game");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Initializing...\n");

        $gamesheet = $this->chooseGamesheet($input, $output);

        $output->writeln('You chose: ' . $gamesheet->getName());

        $this->startGameLoop($input, $output, $gamesheet, []);

        return 0;
    }

    public function chooseGamesheet(InputInterface $input, OutputInterface $output): mixed
    {
        $questionHelper = $this->getHelper('question');
        $sheetDir = __DIR__ . '/../../gamesheets/';

        $choices = array_map(fn ($file) => basename($file), glob($sheetDir . '*.json'));

        $q = new ChoiceQuestion('Please choose a Gamesheet to play:', $choices, 0);

        $sheet = $questionHelper->ask($input, $output, $q);

        return new Gamesheet(json_decode(file_get_contents($sheetDir . $sheet), JSON_OBJECT_AS_ARRAY));
    }

    protected function startGameLoop(InputInterface $input, OutputInterface $output, Gamesheet $gamesheet, $players)
    {
        $stats = $gamesheet->getStats();
        $rules = $gamesheet->getRules();
        $events = $gamesheet->getEvents();

        $currentStats = array_fill_keys($stats, 0);
        $pastRolls = [];

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $rollQuestion = new Question("Please roll a d6 and enter the result (or press enter for random)", 0);

        $rollCheck = [];

        foreach ($events as $eventName => $event) {
            foreach ($event['Rolls'] ?? [] as $roll) {
                $rollCheck[$roll] = $eventName;
            }
        }

        while (!$outcome = $this->checkOutcomes($rules, $currentStats, $pastRolls)) {

            $this->showStats($currentStats, $output);

            $roll = $this->getRoll($questionHelper, $input, $output, $rollQuestion);
            $pastRolls[] = $roll;

            $output->writeln("You rolled: " . $roll . ' which is: ' . ($rollCheck[$roll] ?? 'unknown'));

            $eventRollCheck = [];
            $currentEvent = $events[$rollCheck[$roll]];
            $innerEvents = $currentEvent['Events'];

            $innerRoll = $this->getRoll(clone $questionHelper, $input, $output, clone $rollQuestion);
            $pastRolls[] = $innerRoll;

            foreach ($innerEvents as $innerEventName => $innerEvent) {
                foreach ($innerEvent['Rolls'] ?? [] as $possibleInnerRoll) {
                    $eventRollCheck[$possibleInnerRoll] = $innerEventName;
                }
            }

            $innerEvent = $innerEvents[$eventRollCheck[$innerRoll]];
            $output->writeln("You rolled: " . $innerRoll . ' which means ' . ($eventRollCheck[$innerRoll] ?? 'unknown'));

            foreach ($innerEvent['Stats'] as $statCategory => $value) {
                $currentStats[$statCategory] += $value;
            }

            $output->writeln('');
        }

        $this->showStats($currentStats, $output);
        $output->writeln(
            [
                '',
                $outcome,
            ]
        );
    }

    protected function checkOutcomes(array $rules, array $currentStats, array $pastRolls): ?string
    {
        foreach ($rules as $rule) {
            $conditions = $rule['Condition'] ?? [];
            foreach ($conditions as $conditionName => $condition) {
                // Check the past rolls to see if there's a pattern of 6s
                if ($conditionName === 'Rolls') {
                    // checkOutcomes only runs once per outer loop, so we have to factor in the inner rolls as well
                    $checkArray1 = array_slice($pastRolls, count($condition) * -1);
                    $checkArray2 = array_slice($pastRolls, (count($condition) + 1) * -1, count($condition));

                    if ($checkArray1 == $condition || $checkArray2 == $condition) {
                        return 'Game Over - ' . $rule['Outcome'] . ' ' . $rule['Description'];
                    }
                }

                if (($currentStats[$conditionName] ?? 0) >= $condition) {
                    return 'Game Over - ' . $rule['Outcome'] . ' ' . $rule['Description'];
                }
            }
        }

        return null;
    }

    protected function showStats($currentStats, $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Stat', 'Value']);
        $table->addRows(array_map(fn ($key, $val) => [$key, $val], array_keys($currentStats), $currentStats));

        $table->render();
    }
}