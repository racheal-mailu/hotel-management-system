<?php
header("Content-Type: application/json");
session_start();

// --- DB Connection ---
$conn = new mysqli("localhost", "root", "", "hotel_management");
if ($conn->connect_error) {
    echo json_encode(["reply" => "Database connection failed."]);
    exit;
}

$userMessage = strtolower(trim($_POST['message']));
$response = "";

// --- Static FAQs with multiple responses ---
$faqs = [
    "check in" => [
        "Our check-in time starts at 2:00 PM. Feel free to arrive anytime after that.",
        "You can check in from 2:00 PM onwards. Early check-in may be available on request."
    ],
    "check out" => [
        "Check-out time is before 11:00 AM. Late check-out may be arranged.",
        "Please check out by 11:00 AM to allow us to prepare the rooms for the next guests."
    ],
    "services" => [
        "We offer free Wi-Fi, 24/7 room service, a swimming pool, and an in-house restaurant.",
        "Our guests can enjoy Wi-Fi, swimming pool, restaurant, and 24/7 room service."
    ],
    "location" => [
        "We are located in Juja, just off Thika Road.",
        "Our hotel is conveniently located in Juja, near Thika Road."
    ]
];

// --- Follow-up suggestions ---
$suggestions = [
    "check in" => "You can also ask about available rooms or our services.",
    "check out" => "Would you like to know about early check-out options?",
    "services" => "You can also ask about room availability or view our menu.",
    "location" => "Need directions? You can ask for that too."
];

// --- Check FAQs ---
foreach ($faqs as $key => $options) {
    if (strpos($userMessage, $key) !== false) {
        $response = $options[array_rand($options)]; // random answer
        if (isset($suggestions[$key])) {
            $response .= " " . $suggestions[$key];
        }
        break;
    }
}

// --- DB Queries for dynamic info ---
if ($response == "") {
    if (strpos($userMessage, "available rooms") !== false) {
        $sql = "SELECT room_number, room_type, price_per_night FROM rooms WHERE status='available'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $rooms = [];
            while ($row = $result->fetch_assoc()) {
                $rooms[] = "{$row['room_number']} - {$row['room_type']} @ KES {$row['price_per_night']}";
            }
            $response = "Here are the rooms currently available:\n" . implode("\n", $rooms);
            $response .= "\nYou can type 'booking' to reserve one!";
        } else {
            $response = "Sorry, no rooms are available right now. You can check back later or contact our staff.";
        }
    } elseif (strpos($userMessage, "menu") !== false) {
        $sql = "SELECT name, price FROM menu_items";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = "{$row['name']} - KES {$row['price']}";
            }
            $response = "Here’s our current menu:\n" . implode("\n", $items);
            $response .= "\nYou can ask for daily specials or place an order.";
        } else {
            $response = "Our menu is currently empty. Please check back later!";
        }
    } elseif (strpos($userMessage, "booking status") !== false && isset($_SESSION['booking_id'])) {
        $bookingId = $_SESSION['booking_id'];
        $sql = "SELECT status FROM bookings WHERE booking_id=$bookingId";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response = "Your booking status is: " . $row['status'] . ".";
            $response .= " You can ask for more details or make changes if needed.";
        } else {
            $response = "I couldn’t find your booking. Please check your booking ID or contact support.";
        }
    }
}

// --- Dynamic fallback responses ---
if ($response == "") {
    $fallbacks = [
        "Sorry, I didn’t quite get that. You can ask about check-in/out, services, location, available rooms, menu, or your booking status.",
        "Hmm, I’m not sure I understand. Try asking about rooms, check-in, check-out, or our services.",
        "I’m here to help! You can ask me about available rooms, menu, check-in/out times, or your booking status."
    ];
    $response = $fallbacks[array_rand($fallbacks)];
}

// Return JSON
echo json_encode(["reply" => $response]);
$conn->close();
?>
