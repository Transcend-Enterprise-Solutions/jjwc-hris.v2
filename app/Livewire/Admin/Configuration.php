<?php

namespace App\Livewire\Admin;

use App\Models\BiometricConnection;
use Exception;
use Livewire\Component;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Configuration')]
class Configuration extends Component
{
    public $editCon;
    public $username;
    public $password;
    public $authUrl;
    public $conMessage;
    public $successCon;
    public $conStatus;
    protected $client;


    public function mount()
    {
        $this->getBioCon();
    }

    public function getBioCon()
    {
        $bioCon = BiometricConnection::first();
        if($bioCon){
            $this->username = $bioCon->username;
            $this->password = $bioCon->password;
            $this->authUrl = $bioCon->auth_url;
            $this->conStatus = $bioCon->status;
        }
    }

    public function render()
    {
        return view('livewire.admin.configuration');
    }

    public function testConnection()
    {
        $this->conMessage = null;

        try {
            $this->client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'allow_redirects' => true,
            ]);
    
            $response = $this->client->post($this->authUrl, [
                'json' => [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ]);
    
            $data = json_decode($response->getBody(), true);
    
            if (isset($data['token']) && !empty($data['token'])) {
                $this->successCon = true;
                $this->conStatus = true;
                $this->conMessage = 'Connected successfully!';
            } else {
                $this->successCon = false;
                $this->conStatus = false;
                $this->conMessage = "Connection failed. Authentication was unsuccessful.";
            }
    
        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $errorMessage = '';
    
            if ($statusCode === 400) {
                $errorMessage = "Invalid username or password.";
            } elseif ($statusCode === 404) {
                $errorMessage = "Invalid authentication URL.";
            } elseif ($statusCode === 500) {
                $errorMessage = "Server error. Please try again later.";
            } else {
                $errorMessage = "Unexpected error occurred.";
            }
    
            $this->successCon = false;
            $this->conStatus = false;
            $this->conMessage = "Connection failed: [{$statusCode}] {$errorMessage}";
        } catch (Exception $e) {
            $this->successCon = false;
            $this->conStatus = false;
            $this->conMessage = "Unexpected error: " . $e->getMessage();
        } 
    }

    public function saveConnection()
    {
        try{
            $this->validate([
                'username' => 'required',
                'password' => 'required',
                'authUrl' => 'required',
            ]);

            $bioCon = BiometricConnection::first();

            $this->testConnection();

            if($this->successCon === false){
                $this->dispatch('swal', [
                    'title' => 'Connection failed. Please test connection before saving!',
                    'icon' => 'error'
                ]);
                return;
            }

            if($bioCon){
                $bioCon->update([
                    'username' => $this->username,
                    'password' => $this->password,
                    'auth_url' => $this->authUrl,
                    'status' => $this->conStatus,
                ]);
                $this->resetVariables();
                $this->dispatch('swal', [
                    'title' => 'Biometric connection updated successfully!',
                    'icon' => 'success'
                ]);
                $this->getBioCon();
                return;
            }

            BiometricConnection::create([
                'username' => $this->username,
                'password' => $this->password,
                'auth_url' => $this->authUrl,
                'status' => false,
            ]);

            $this->resetVariables();
            $this->getBioCon();
            $this->dispatch('swal', [
                'title' => 'Biometric connection saved successfully!',
                'icon' => 'success'
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleEditConnection(){
        if ($this->editCon){
            $this->resetVariables();
            $this->getBioCon();
        } else {
            $this->editCon = true;
        }
    }

    public function closeMessage(){
        $this->conMessage = null;
    }

    public function resetVariables()
    {
        $this->editCon = null;
        $this->username = null;
        $this->password = null;
        $this->authUrl = null;
        $this->conMessage = null;
        $this->successCon = null;
        $this->conStatus = null;
    }
}
