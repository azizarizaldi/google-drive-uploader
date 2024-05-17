<?php
require 'vendor/autoload.php';
session_start();
$client = new Google\Client();
$client->setAuthConfig('client_credentials.json');
$client->addScope(Google\Service\Drive::DRIVE);
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$client->setRedirectUri($redirect_uri);

if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
} else {
    try {
        if(!isset($_SESSION['data']['access_token'])) {
            $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $_SESSION['data'] = $accessToken;
            $token = $accessToken;
        } else {
            $token = $_SESSION['data']['access_token'];
        }

        $client->setAccessToken($token);
        
        // Create a Drive service object
        $service = new Google_Service_Drive($client);

        $pageToken = isset($_GET['pageToken']) ? $_GET['pageToken'] : null;        
        // Print the names and IDs for up to 10 files
        $optParams = array(
            'pageSize' => 10,
            'pageToken' => $pageToken,
            'fields' => 'nextPageToken, files(id, name, mimeType, description, createdTime, modifiedTime, size, webViewLink, webContentLink, owners, parents, shared, permissions)'
        );
 
// Execute the request
$results = $service->files->listFiles($optParams);

// Start HTML output
echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Google Drive Files</title>
</head>
<body>
<h1>Google Drive Files</h1>
<table border="1">
  <thead>
    <tr>
      <th>No</th>
      <th>Name</th>
      <th>ID</th>
      <th>Mime Type</th>
      <th>Description</th>
      <th>Created Time</th>
      <th>Modified Time</th>
      <th>Size</th>
      <th>Web View Link</th>
      <th>Web Content Link</th>
      <th>Owners</th>
      <th>Parents</th>
      <th>Shared</th>
      <th>Permissions</th>
    </tr>
  </thead>
  <tbody>';

// Check if there are files
if (count($results->getFiles()) == 0) {
    echo "<tr><td colspan='13'>No files found.</td></tr>";
} else {
    $nomor = 0;
    // Iterate over files and create table rows
    foreach ($results->getFiles() as $file) {
        echo '<tr>';
        echo '<td>' . ++$nomor . '</td>';
        echo '<td>' . htmlspecialchars($file->getName()) . '</td>';
        echo '<td>' . htmlspecialchars($file->getId()) . '</td>';
        echo '<td>' . htmlspecialchars($file->getMimeType()) . '</td>';
        echo '<td>' . htmlspecialchars($file->getDescription()) . '</td>';
        echo '<td>' . htmlspecialchars($file->getCreatedTime()) . '</td>';
        echo '<td>' . htmlspecialchars($file->getModifiedTime()) . '</td>';
        echo '<td>' . htmlspecialchars($file->getSize()) . '</td>';
        echo '<td><a href="' . htmlspecialchars($file->getWebViewLink()) . '" target="_blank">View</a></td>';
        echo '<td><a href="' . htmlspecialchars($file->getWebContentLink()) . '" target="_blank">Download</a></td>';

        // Owners
        $owners = $file->getOwners();
        $ownerNames = array();
        foreach ($owners as $owner) {
            $ownerNames[] = htmlspecialchars($owner->getDisplayName());
        }
        echo '<td>' . implode(", ", $ownerNames) . '</td>';

        // Parents
        $parents = $file->getParents();
        echo '<td>' . ($parents ? implode(", ", $parents) : 'None') . '</td>';

        // Shared
        echo '<td>' . ($file->getShared() ? 'Yes' : 'No') . '</td>';

        // Permissions
        $permissions = $file->getPermissions();
        $permissionRoles = array();
        foreach ($permissions as $permission) {
            $permissionRoles[] = htmlspecialchars($permission->getRole());
        }
        echo '<td>' . implode(", ", $permissionRoles) . '</td>';

        echo '</tr>';
    }
}

// End HTML output
echo '</tbody>
</table>
</body>
</html>';        

// Pagination links
if ($results->getNextPageToken()) {
    echo '<a href="?pageToken=' . $results->getNextPageToken() . '">Next Page - '.$results->getNextPageToken().'</a>';
}
        
    } catch (Exception $e) {
        echo 'Error during authentication: ' . $e->getMessage();
        error_log($e->getMessage());
    }  
}
?>
