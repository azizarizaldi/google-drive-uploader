<?php
session_start();
require 'vendor/autoload.php';

class Callback {
    private $client;

    public function __construct() {
        $this->client = new Google\Client();
        $this->client->setAuthConfig('client_credentials.json');
        $this->client->addScope(Google\Service\Drive::DRIVE);
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/callback.php';
        $this->client->setRedirectUri($redirect_uri);
        $this->client->setRedirectUri($redirect_uri);
        $this->client->setAccessType('offline');
    }

    public function process($code="") {    
        try {
            $accessToken    = $this->client->fetchAccessTokenWithAuthCode($code);

            if(isset($accessToken['access_token'])) {
                $_SESSION       = $accessToken;
                $redirect_uri   = 'http://' . $_SERVER['HTTP_HOST'] . '/home.php';
                header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
            } else {
                $data['status']  = false;
                $data['message'] = "Failed to verify account";
                echo json_encode($data);
            }
        } catch(Exception $e) {
            $data['status']  = false;
            $data['message'] = "Error: ". $e->getMessage();    
            echo json_encode($data);
        }
    }
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $callback = new Callback();
    $callback->process($_GET['code']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
