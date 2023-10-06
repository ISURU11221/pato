<?php
// Database connection parameters
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'Modayakek@1234';
$db_name = 'pato';

// Create a database connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $reservation_date = $_POST["date"];
    $reservation_time = $_POST["time"];
    $num_people = $_POST["people"];
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];

    $date_object = new DateTime($reservation_date);

    // Format the DateTime object using the database required date format.
    $formatted_date_string = $date_object->format('Y-m-d');

    // Basic date and time validation
    $current_date = date("Y-m-d");
    $current_time = date("H:i:s");
    if ($formatted_date_string < $current_date || ($formatted_date_string == $current_date && $reservation_time <= $current_time)) {
        echo "Invalid reservation date or time. Please select a future date and time.";
        exit();
    }

    // Basic table availability check (you may need to implement a more complex logic)
    $max_capacity = 50; // Maximum capacity of your restaurant
    $existing_reservations = 0; // Retrieve the actual number of reservations for the chosen date and time from the database

    if (($existing_reservations + $num_people) > $max_capacity) {
        echo "Sorry, we don't have enough available tables for your party size at the chosen date and time.";
        exit();
    }

    // Insert data into the reservations table
    $sql = "INSERT INTO reservation (name,phone,email ,reservation_time,num_people ,reservation_date)
            VALUES ('$name','$phone' , '$email','$reservation_time','$num_people', '$formatted_date_string')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiss", $formatted_date_string, $reservation_time, $num_people, $name, $phone, $email);

    if ($stmt->execute()) {
        echo "Reservation successful!";
        // You can send a confirmation email or notification here
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
