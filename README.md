# zomato-api-wrapper

PHP wrapper for Zomato API

## Requirements

- PHP 5.5 or greater

## Installation

**Via Composer**

```shell
$ composer require shalompeace/zomato-api-wrapper
```

## Usage

```shell
<?php 
    // Require composer autoloader
    require_once 'vendor/autoload.php';
    
    // Initialize Zomato API
    $zomato = new Zomato\Api\Zomato('Enter API key');
    
    // Get list of Categories
    $categories = $zomato->categories();
    
    // Get city details
    $cities = $zomato->cities(['q' => 'City name']);
    
    // Get Zomato collections in a city
    $collections = $zomato->collections(['city_id' => 63]);
    
    // Get list of all cuisines in a city
    $cuisines = $zomato->cuisines(['city_id' => 63]);
    
    // Get list of restaurant types in a city
    $establishments = $zomato->establishments(['city_id' => 63]);
    
    // Get location details based on coordinates
    $geocode = $zomato->geocode(['lat' => '', 'lon' => '']);
    
    // Get Zomato location details
    $location_details = $zomato->location_details(['entity_id' => '', 'entity_type' => '']);
    
    // Search for locations
    $locations = $zomato->locations(['query' => '']);
    
    // Get daily menu of a restaurant
    $dailymenu = $zomato->dailymenu(['res_id' => '']);
    
    // Get restaurant details
    $restaurant = $zomato->restaurant(['res_id' => '']);
    
    // Get restaurant reviews
    $reviews = $zomato->reviews(['res_id' => '']);
    
    // Search for restaurant
    $result = $zomato->search(['q' => '']);
```