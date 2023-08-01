<?php

namespace Starships;

use Exception;

class ApiWrapper
{
    /**
     * Loads a starship from the SWAPI API.
     *
     * @param int $index The index of the starship to load.
     * @return StarshipModel
     * @throws Exception If there is an error fetching data from the API.
     */
    public static function getShip(int $index): StarshipModel
    {
        $url = "https://swapi.dev/api/starships/$index/";

        $response = self::request($url);

        return StarshipModel::fromJson(json_decode($response, true));
    }

    /**
     * Loads a person from the SWAPI API.
     *
     * @param string $url The URL of the person to load.
     * @return PersonModel
     * @throws Exception If there is an error fetching data from the API.
     */
    public static function getPerson(string $url): PersonModel
    {
        $response = self::request($url);

        return PersonModel::fromJson(json_decode($response, true));
    }

    /**
     * Makes an HTTP GET request to the given URL and returns the response.
     *
     * @param string $url The URL to make the request to.
     * @return string The response from the server.
     * @throws Exception If there is an error fetching data from the API.
     */
    private static function request(string $url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch) || $httpCode !== 200) {
            $error = empty(curl_error($ch)) ? $response : curl_error($ch);
            throw new Exception("Error fetching $url: $error");
        }

        curl_close($ch);

        return $response;
    }
}