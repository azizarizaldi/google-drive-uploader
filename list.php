<?php
session_start();
require 'vendor/autoload.php';

class ListData {
    private $client;

    public function __construct() {
        $this->client = new Google\Client();
        $this->client->setAuthConfig('client_credentials.json');
        $this->client->addScope(Google\Service\Drive::DRIVE);
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/callback.php';
        $this->client->setRedirectUri($redirect_uri);
        $this->client->setAccessType('offline');
    }

    public function list_data() {    
        try {
            $access_token = $_SESSION['access_token'];
            $this->client->setAccessToken($access_token);

            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                }
            }

            $service    = new Google_Service_Drive($this->client);
            $pageToken  = isset($_GET['pageToken']) ? $_GET['pageToken'] : null;        
            $keyword    = isset($_GET['keyword']) ? $_GET['keyword'] : '';

            $optParams  = array(
                'pageSize'  => 10,
                'q'         => "name contains '$keyword'",
                'pageToken' => $pageToken,
                'fields'    => 'nextPageToken, files(id, name, mimeType, description, createdTime, modifiedTime, size, webViewLink, webContentLink, owners, parents, shared, permissions)'
            );
    
            $results = $service->files->listFiles($optParams);

            $data       = array();
            $response   = array();
            $response['status']  = false;
            $response['message']  = "Data tidak ditemukan";
            $next_page_token = null;
            if(count($results->getFiles()) > 0) {
                foreach ($results->getFiles() as $file) {
                    $temp_data['file_name'] = htmlspecialchars($file->getName());
                    $temp_data['file_id'] = htmlspecialchars($file->getId());
                    $temp_data['mime_type'] = htmlspecialchars($file->getMimeType());
                    $temp_data['description'] = htmlspecialchars($file->getDescription());
                    $temp_data['created_time'] = htmlspecialchars($file->getCreatedTime());
                    $temp_data['modified_time'] = htmlspecialchars($file->getModifiedTime());
                    $temp_data['web_link'] = htmlspecialchars($file->getWebViewLink());
                    $temp_data['content_link'] = htmlspecialchars($file->getWebContentLink());

                    $owners = $file->getOwners();
                    $ownerNames = array();
                    foreach ($owners as $owner) {
                        $ownerNames[] = htmlspecialchars($owner->getDisplayName());
                    }

                    $temp_data['owners'] = implode(", ", $ownerNames);

                    $parents = $file->getParents();
                    $temp_data['parents'] = ($parents ? implode(", ", $parents) : 'None');

                    $permissions = $file->getPermissions();
                    $permissionRoles = array();
                    foreach ($permissions as $permission) {
                        $permissionRoles[] = htmlspecialchars($permission->getRole());
                    }

                    $temp_data['permissions'] = implode(", ", $permissionRoles);
                    $data[] = $temp_data;
                }
                $response['status']  = true;
                $response['message']  = "Data ditemukan";    
                $next_page_token = $results->getNextPageToken();
            }

            $response['data'] = $data;
            $response['next_page_token'] = $next_page_token;
            echo json_encode($response);
        } catch(Exception $e) {
            $exception = json_decode($e->getMessage());

            $data['status']  = false;
            $data['message'] = $exception->error->message;
            echo json_encode($data);
        }
    }
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = new ListData();
    $data->list_data();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
