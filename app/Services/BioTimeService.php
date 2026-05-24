<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\AuditLog;
use App\Models\BiometricConnection;

class BioTimeService
{
    protected $client;
    protected $authUrl;
    protected $username;
    protected $password;
    protected $token;
    protected $hostPort;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'allow_redirects' => true,
        ]);
    }

    // Function to authenticate and get JWT token
    public function authenticate()
    {
        // Fetch the credentials from the database
        $bioCon = BiometricConnection::first();
        if ($bioCon) {
            $host = explode('/', $bioCon->auth_url);

            $this->username = $bioCon->username;
            $this->password = $bioCon->password;
            $this->authUrl = $bioCon->auth_url;
            $this->hostPort = $host[2];

        } else {
            $this->username = null;
            $this->password = null;
            $this->authUrl = null;
        }

        if ($this->token) {
            return;
        }

        try {
            $response = $this->client->post($this->authUrl, [
                'json' => [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ]);
            $data = json_decode($response->getBody(), true);
            $this->token = $data['token'];
        } catch (RequestException $e) {
            $this->logError('auth_error', 'Error authenticating', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest()->getBody()->getContents(),
                'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            // Instead of throwing an exception, we'll just set the token to null
            $this->token = null;
        }
    }

    public function getTransactions($params = [])
    {
        $this->authenticate(); // Try to authenticate before each request

        if (!$this->token) {
            // If we couldn't authenticate, log an error and return an empty array
            $this->logError('fetch_error', 'Unable to fetch transactions due to authentication failure', []);
            return [];
        }

        try {
            $response = $this->client->get('http://' . $this->hostPort . '/iclock/api/transactions/', [
                'headers' => [
                    'Authorization' => 'JWT ' . $this->token,
                ],
                'query' => $params
            ]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $this->logError('fetch_error', 'Error fetching transactions', [
                'message' => $e->getMessage(),
                'params' => $params,
                'request' => $e->getRequest()->getBody()->getContents(),
                'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            return [];
        }
    }

    private function logError($type, $message, $context)
    {
        AuditLog::create([
            'type' => $type,
            'message' => $message,
            'context' => $context,
        ]);
    }
}
