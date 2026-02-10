<?php

namespace App\Services\Search;

interface SearchEngineInterface
{
    /**
     * Index a single document
     */
    public function index(string $type, int $id, array $data): bool;

    /**
     * Search for documents
     * Returns array of results (['id' => 1, 'score' => 1.5, ...])
     */
    public function search(string $query, string $type = null): array;

    /**
     * Delete a document from index
     */
    public function delete(string $type, int $id): bool;
}
