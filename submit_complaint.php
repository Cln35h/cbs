<?php
session_start();

// Create MySQL connection
$servername = "yours";
$username = "yours";
$password = "yours";
$dbname = "yours";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle file upload and return the uploaded file path
function handleFileUpload($fieldName){
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES[$fieldName]['tmp_name'];
        $fileName = $_FILES[$fieldName]['name'];
        $fileType = $_FILES[$fieldName]['type'];

        // Choose the destination folder based on the file type
        $destinationFolder = '';
        if (strpos($fileType, 'image/') === 0) {
            $destinationFolder = 'uploads/images/';
        } elseif (strpos($fileType, 'video/') === 0) {
            $destinationFolder = 'uploads/videos/';
        } elseif (strpos($fileType, 'audio/') === 0) {
            $destinationFolder = 'uploads/audios/';
        } else {
            $destinationFolder = 'uploads/documents/';
        }

        // Create the destination folder if it doesn't exist
        if (!file_exists($destinationFolder)) {
            mkdir($destinationFolder, 0755, true);
        }

        $fileDestination = $destinationFolder . $fileName;

        if (move_uploaded_file($fileTmpPath, $fileDestination)) {
            return $fileDestination;
        } else {
            // Return an error message if file upload fails
            return "Error uploading $fieldName.";
        }
    }
    return null;
}

// CAPTCHA verification function
function verifyCaptcha($userInput) {
    if (isset($_SESSION['captcha']) && !empty($userInput)) {
        $storedCaptcha = $_SESSION['captcha'];
        unset($_SESSION['captcha']); // Clean up the session after verification (whether successful or not)
        return strtolower($userInput) === strtolower($storedCaptcha);
    }
    return false;
}

// Process form data and store it in the database
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize the form data (implement your validation logic here)
    $state = isset($_POST["state"]) ? $_POST["state"] : "";
    $district = isset($_POST["district"]) ? $_POST["district"] : "";
    $incident_description = isset($_POST["incident_description"]) ? $_POST["incident_description"] : "";
    $individual_organization = isset($_POST["individual_organization"]) ? $_POST["individual_organization"] : "";
    $date = isset($_POST["date"]) ? $_POST["date"] : "";
    $time = isset($_POST["time"]) ? $_POST["time"] : "";
    $location = isset($_POST["location"]) ? $_POST["location"] : "";
    $additional_details = isset($_POST["additional_details"]) ? $_POST["additional_details"] : "";

    // CAPTCHA verification
    $captchaValue = isset($_POST['captcha']) ? $_POST['captcha'] : '';
    if (!verifyCaptcha($captchaValue)) {
        // CAPTCHA verification failed
        $response = array('success' => false, 'message' => 'Incorrect CAPTCHA! Please try again.');
        echo json_encode($response);
        exit; // Stop further execution
    }

    // Handle file uploads and get the file paths
    $image_file = handleFileUpload('image_file');
    $video_file = handleFileUpload('video_file');
    $audio_file = handleFileUpload('audio_file');
    $document_file = handleFileUpload('document_file');

    $currentDateTime = date("Y-m-d H:i:s"); // Get the current date and time

    // Use prepared statements to insert data into the database
    $stmt = $conn->prepare("INSERT INTO complaint (state, district, incident_description, individual_organization, date, time, location, additional_details, image_file, video_file, audio_file, document_file, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
if ($stmt) {
// Bind the parameters and execute the statement
$stmt->bind_param(
"sssssssssssss",
$state,
$district,
$incident_description,
$individual_organization,
$date,
$time,
$location,
$additional_details,
$image_file,
$video_file,
$audio_file,
$document_file,
$currentDateTime // Add the current date and time to the query
);

if ($stmt->execute()) {
$stmt->close();
$conn->close();
// Show success message as a JSON response
header('Content-Type: application/json');
$response = array('success' => true, 'message' => 'Complaint submitted successfully!');
echo json_encode($response);
exit; // Make sure to exit after sending the JSON response
} else {
// Show error message as a JSON response
$response = array('success' => false, 'message' => 'Error executing query.');
echo json_encode($response);
exit;
}
} else {
// Show error message as a JSON response
$response = array('success' => false, 'message' => 'Error preparing statement.');
echo json_encode($response);
exit;
}
}
?>