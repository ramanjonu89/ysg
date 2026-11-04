<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;
use Exception;

class ElasticsearchDevToolController extends Controller
{
    private $elasticsearchClient;

    public function __construct()
    {
        $this->elasticsearchClient = $this->createElasticsearchClient();
    }

    private function createElasticsearchClient()
    {
        $config = config('elastic.client');
        
        $clientBuilder = ClientBuilder::create()
            ->setHosts($config['Hosts']);

        if (isset($config['BasicAuthentication'])) {
            $clientBuilder->setBasicAuthentication(
                $config['BasicAuthentication'][0],
                $config['BasicAuthentication'][1]
            );
        }

        if (isset($config['sslVerification'])) {
            $clientBuilder->setSSLVerification($config['sslVerification']);
        }

        return $clientBuilder->build();
    }

    public function index()
    {
        return view('admin.elasticsearch.dev_tools');
    }

    public function executeQuery(Request $request)
    {
        try {
            $queryString = $request->input('query');
            
            // Parse the query string giống Kibana Dev Tools
            $parsedQueries = $this->parseKibanaQuery($queryString);
            
            if (empty($parsedQueries)) {
                return response()->json([
                    'success' => false,
                    'error' => 'No valid queries found'
                ], 400);
            }

            $results = [];
            
            // Thực hiện từng query
            foreach ($parsedQueries as $query) {
                $result = $this->executeSingleQuery($query);
                $results[] = $result;
            }

            // Nếu chỉ có 1 query, trả về kết quả trực tiếp
            if (count($results) === 1) {
                return response()->json([
                    'success' => true,
                    'response' => $results[0],
                    'execution_time' => microtime(true) - LARAVEL_START
                ]);
            }

            // Nếu có nhiều query, trả về array kết quả
            return response()->json([
                'success' => true,
                'response' => $results,
                'execution_time' => microtime(true) - LARAVEL_START
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'execution_time' => microtime(true) - LARAVEL_START
            ], 500);
        }
    }

    private function parseKibanaQuery($queryString)
    {
        $lines = explode("\n", trim($queryString));
        $queries = [];
        $currentQuery = null;
        $bodyLines = [];

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip empty lines and comments
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            // Check if line starts with HTTP method
            if (preg_match('/^(GET|POST|PUT|DELETE)\s+(.+)$/', $line, $matches)) {
                // Save previous query if exists
                if ($currentQuery) {
                    $currentQuery['body'] = $this->parseBody($bodyLines);
                    $queries[] = $currentQuery;
                    $bodyLines = [];
                }

                // Start new query
                $currentQuery = [
                    'method' => $matches[1],
                    'path' => $matches[2]
                ];
            } else {
                // This is part of request body
                $bodyLines[] = $line;
            }
        }

        // Add last query
        if ($currentQuery) {
            $currentQuery['body'] = $this->parseBody($bodyLines);
            $queries[] = $currentQuery;
        }

        return $queries;
    }

    private function parseBody($bodyLines)
    {
        if (empty($bodyLines)) {
            return null;
        }

        $bodyJson = implode("\n", $bodyLines);
        
        try {
            return json_decode($bodyJson, true);
        } catch (Exception $e) {
            return null;
        }
    }

    private function executeSingleQuery($query)
    {
        $method = strtoupper($query['method']);
        $path = $query['path'];
        $body = $query['body'] ?? [];

        // Parse index from path
        $pathParts = explode('/', trim($path, '/'));
        $index = $pathParts[0] ?? '';
        
        $params = [];
        if (!empty($index) && !in_array($index, ['_cluster', '_cat', '_nodes'])) {
            $params['index'] = $index;
        }

        if (!empty($body)) {
            $params['body'] = $body;
        }

        // Execute based on path and method
        switch (true) {
            case strpos($path, '_cluster/health') !== false:
                return $this->elasticsearchClient->cluster()->health();

            case strpos($path, '_cluster/stats') !== false:
                return $this->elasticsearchClient->cluster()->stats();

            case strpos($path, '_cat/indices') !== false:
                return $this->elasticsearchClient->cat()->indices(['format' => 'json']);

            case strpos($path, '_cat/health') !== false:
                return $this->elasticsearchClient->cat()->health(['format' => 'json']);

            case strpos($path, '_cat/nodes') !== false:
                return $this->elasticsearchClient->cat()->nodes(['format' => 'json']);

            case strpos($path, '_search') !== false:
                return $this->elasticsearchClient->search($params);

            case strpos($path, '_mapping') !== false && $method === 'GET':
                return $this->elasticsearchClient->indices()->getMapping($params);

            case strpos($path, '_count') !== false:
                return $this->elasticsearchClient->count($params);

            case $method === 'GET' && !empty($index):
                if (count($pathParts) > 1) {
                    $params['id'] = $pathParts[1];
                    return $this->elasticsearchClient->get($params);
                } else {
                    return $this->elasticsearchClient->indices()->get(['index' => $index]);
                }

            case $method === 'POST' && strpos($path, '_search') !== false:
                return $this->elasticsearchClient->search($params);

            case $method === 'PUT':
                if (strpos($path, '_mapping') !== false) {
                    return $this->elasticsearchClient->indices()->putMapping($params);
                } else {
                    return $this->elasticsearchClient->index($params);
                }

            case $method === 'DELETE':
                if (count($pathParts) === 1) {
                    return $this->elasticsearchClient->indices()->delete(['index' => $index]);
                } else {
                    $params['id'] = $pathParts[1];
                    return $this->elasticsearchClient->delete($params);
                }

            default:
                throw new Exception("Unsupported operation: {$method} {$path}");
        }
    }

    public function getClusterInfo()
    {
        try {
            $clusterHealth = $this->elasticsearchClient->cluster()->health();
            $clusterStats = $this->elasticsearchClient->cluster()->stats();
            $indices = $this->elasticsearchClient->cat()->indices(['format' => 'json']);

            return response()->json([
                'success' => true,
                'cluster_health' => $clusterHealth,
                'cluster_stats' => $clusterStats,
                'indices' => $indices
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function parseQuery(Request $request)
    {
        try {
            $queryString = $request->input('query');
            
            // Parse query string giống Kibana Dev Tools
            $lines = explode("\n", trim($queryString));
            $queries = [];
            $currentQuery = null;
            $bodyLines = [];

            foreach ($lines as $line) {
                $line = trim($line);
                
                if (empty($line)) {
                    continue;
                }

                // Nếu line bắt đầu bằng HTTP method
                if (preg_match('/^(GET|POST|PUT|DELETE)\s+(.+)$/', $line, $matches)) {
                    // Save previous query if exists
                    if ($currentQuery) {
                        $currentQuery['body'] = implode("\n", $bodyLines);
                        $queries[] = $currentQuery;
                        $bodyLines = [];
                    }

                    // Start new query
                    $currentQuery = [
                        'method' => $matches[1],
                        'path' => $matches[2]
                    ];
                } else {
                    // This is part of request body
                    $bodyLines[] = $line;
                }
            }

            // Add last query
            if ($currentQuery) {
                $currentQuery['body'] = implode("\n", $bodyLines);
                $queries[] = $currentQuery;
            }

            return response()->json([
                'success' => true,
                'queries' => $queries
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 