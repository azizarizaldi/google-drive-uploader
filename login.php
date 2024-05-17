<?php
session_start();
require 'vendor/autoload.php';

class Login {
    private $client;

    public function __construct() {
        $this->client = new Google\Client();
        $this->client->setAuthConfig('client_credentials.json');
        $this->client->addScope(Google\Service\Drive::DRIVE);
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/callback.php';
        $this->client->setRedirectUri($redirect_uri);
        $this->client->setAccessType('offline');
    }

    public function auth_google() {
        $authUrl = $this->client->createAuthUrl();
    
        $data['status']  = true;
        $data['message'] = "Authorize your account";
        $data['data']    = filter_var($authUrl, FILTER_SANITIZE_URL);
        echo json_encode($data);
    }
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = new Login();
    $login->auth_google();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
