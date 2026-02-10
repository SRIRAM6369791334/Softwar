<?php

namespace App\Services\Search;

class ElasticsearchEngine implements SearchEngineInterface
{
    private $host = 'http://localhost:9200';

    public function index(string $type, int $id, array $data): bool
    {
        return $this->call("PUT", "/{$type}/_doc/{$id}", $data)['result'] ?? false;
    }

    public function search(string $query, string $type = null): array
    {
        $path = $type ? "/{$type}/_search" : "/_search";
        $body = [
            "query" => [
                "multi_match" => [
                    "query" => $query,
                    "fields" => ["name^3", "sku", "description"],
                    "fuzziness" => "AUTO"
                ]
            ]
        ];

        $response = $this->call("GET", $path, $body);
        
        // Transform ES response to unified format
        $results = [];
        if (isset($response['hits']['hits'])) {
            foreach ($response['hits']['hits'] as $hit) {
                $results[] = [
                    'id' => $hit['_id'],
                    'score' => $hit['_score'],
                    'type' => $hit['_index'],
                    'name' => $hit['_source']['name'] ?? '',
                    'sku' => $hit['_source']['sku'] ?? '',
                ];
            }
        }
        return $results;
    }

    public function delete(string $type, int $id): bool
    {
        return $this->call("DELETE", "/{$type}/_doc/{$id}")['result'] ?? false;
    }

    private function call($method, $path, $data = null)
    {
        $ch = curl_init($this->host . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($result, true);
        }
        return null;
    }
}
