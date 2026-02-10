<?php

namespace App\Services\Search;

class SearchService
{
    private static $engine;

    public static function getEngine(): SearchEngineInterface
    {
        if (self::$engine) {
            return self::$engine;
        }

        // Simulating Config Load
        // In a real app, this comes from a config file.
        // CHECK if ES is running on localhost:9200 via simple timeout check? 
        // Or just default to DB for this demo environment.
        
        $driver = getenv('SEARCH_DRIVER') ?: 'database'; // Default to DB

        if ($driver === 'elastic') {
            self::$engine = new ElasticsearchEngine();
        } else {
            self::$engine = new DatabaseSearchEngine();
        }

        return self::$engine;
    }
}
