<?php

namespace Starships;

/**
 * An individual person or character within the Star Wars universe.
 *
 * @link https://swapi.dev/documentation#people
 */
class PersonModel
{
    /**
     * @var string The name of this person.
     */
    public string $name;
    /**
     * @var int The height of this person in centimeters.
     */
    public int $height;

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
        $model->height = (int)$json['height'];
        return $model;
    }
}