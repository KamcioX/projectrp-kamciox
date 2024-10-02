<?php
$response = ['success' => false, 'message' => 'Unknown error'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['visibility'])) {
        $visibility = $_POST['visibility'] === "true" ? true : false;
        
        $configData = [
            'visibility' => $visibility
        ];
        
        if (file_put_contents('config.json', json_encode($configData))) {
            $response['success'] = true;
            $response['message'] = 'Visibility updated successfully';
        } else {
            $response['message'] = 'Failed to write to config.json';
        }
    } else {
        $response['message'] = 'Visibility parameter not set';
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>
