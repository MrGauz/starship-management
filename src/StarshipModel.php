<?php

namespace Starships;

use Exception;

/**
 * @link https://swapi.dev/documentation#starships
 */
class StarshipModel
{
    /**
     * @var string The name of this starship. The common name, such as "Death Star".
     */
    public string $name;
    /**
     * @var string The model or official name of this starship. Such as "T-65 X-wing" or "DS-1 Orbital Battle Station".
     */
    public string $model;
    /**
     * @var int The maximum number of megalights this starship can travel in a standard hour.
     */
    public int $speed;
    /**
     * @var int The maximum number of kilograms that this starship can transport.
     */
    public int $maxCargoCapacity;
    /**
     * @var string The number of personnel needed to run or pilot this starship.
     */
    public string $crew;
    /**
     * @var PersonModel[] An array of people that this starship has been piloted by.
     */
    public array $pilots;

    /**
     * @var int The weight of current cargo in kilograms.
     */
    public int $cargoWeight = 0;
    /**
     * @var array An array of cargo items currently on board.
     */
    private array $cargo = [];

    /**
     * Create a new instance of this class from a JSON response.
     *
     * @param array $json An associative array representing a JSON response from SWAPI API.
     * @return self
     */
    public static function fromJson(array $json): self
    {
        $model = new self();
        $model->name = $json['name'];
        $model->model = $json['model'];
        $model->speed = (int)$json['MGLT'];
        $model->maxCargoCapacity = (int)$json['cargo_capacity'];
        $model->crew = (int)$json['crew'];
        $model->pilots = array_map(function ($pilotUrl) {
            try {
                return ApiWrapper::getPerson($pilotUrl);
            } catch (Exception $e) {
                echo $e->getMessage() . PHP_EOL;
                return null;
            }
        }, $json['pilots']);
        return $model;
    }

    /**
     * @return string A comma-separated list of pilots or a total crew count.
     */
    public function getCrew(): string
    {
        if (empty($this->pilots)) {
            return $this->crew;
        }

        return implode(', ', array_map(function ($pilot) {
            return is_null($pilot) ? "" : "$pilot->name ($pilot->height cm)";
        }, $this->pilots));
    }

    /**
     * Weight of current cargo in kilograms.
     *
     * @return int The weight of current cargo in kilograms.
     */
    public function getCargoWeight(): int
    {
        return $this->cargoWeight;
    }

    /**
     * Load cargo onto the starship.
     *
     * @param mixed $item The cargo item to load.
     * @param int $weight The weight of the cargo in kilograms.
     * @return void
     * @throws Exception If the cargo exceeds the maximum capacity of the starship.
     */
    public function loadCargo(mixed $item, int $weight): void
    {
        if ($this->cargoWeight + $weight > $this->maxCargoCapacity) {
            throw new Exception("Loaded cargo exceeds capacity: $this->cargoWeight/$this->maxCargoCapacity kg");
        }

        $this->cargoWeight += $weight;
        $this->cargo[] = $item;
    }

    /**
     * Unload cargo from the starship.
     *
     * @param int $index The index of the cargo item to unload.
     * @param int $weight The expected weight of the cargo in kilograms.
     * @return mixed The unloaded cargo item.
     * @throws Exception If the index is out of bounds.
     */
    public function unloadCargo(int $index, int $weight): mixed
    {
        if ($index < 0 || $index >= count($this->cargo)) {
            throw new Exception("Invalid cargo index: $index");
        }

        $item = $this->cargo[$index];
        $this->cargoWeight -= $weight;
        array_splice($this->cargo, $index, 1);
        return $item;
    }
}