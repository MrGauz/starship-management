<?php

require 'vendor/autoload.php';

use AsciiTable\Builder;
use AsciiTable\Exception\BuilderException;
use Starships\ApiWrapper;
use Starships\StarshipModel;

// Load 15 starships from SWAPI API.
/** @var StarshipModel[] $starships */
$starships = [];
$counter = 1;
while (count($starships) < 15) {
    try {
        $starships[] = ApiWrapper::getShip($counter++);
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}

// Sort the starships by their speed.
usort($starships, function ($a, $b) {
    return $b->speed <=> $a->speed;
});
$biggestSpeed = $starships[0]->speed;

// Add some cargo to the starships.
try {
    $starships[0]->loadCargo('Food Supplies', 2000);
    $starships[0]->loadCargo(new StarshipModel(), 10000);
    $starships[1]->loadCargo('Alien Artifacts', 2500);
    $starships[2]->loadCargo(true, 1);
    $starships[3]->loadCargo("Spacecraft Parts", 3000);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

// Display the starships in a table.
$table = new Builder();
$table->setTitle('Starships');
$table->addRows(array_map(function (StarshipModel $s) use ($biggestSpeed) {
    return [
        'Name' => ($s->name == $s->model) ? $s->name : "$s->model aka $s->name",
        'Speed' => "$s->speed MGLT (" . (int)($s->speed / $biggestSpeed * 100) . "%)",
        'Cargo' => $s->getCargoWeight() . "/$s->maxCargoCapacity kg",
        'Crew' => $s->getCrew(),
    ];
}, $starships));
try {
    echo $table->renderTable() . PHP_EOL;
} catch (BuilderException $e) {
    echo "Can't display you the results :c\n" . $e->getMessage() . PHP_EOL;
}