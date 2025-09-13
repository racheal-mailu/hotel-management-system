<?php
include('db_connect.php');

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $msg = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (fullname, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $msg);

    if ($stmt->execute()) {
        $message = "<p style='color:green;'>Thank you for reaching out, $fullname. We will get back to you soon!</p>";
    } else {
        $message = "<p style='color:red;'>Sorry, there was an error submitting your inquiry.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Hotel Lilies</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }
        header {
            background: linear-gradient(90deg, #28a745, #20c997);
            color: white;
            text-align: center;
            padding: 20px;
        }
        nav {
            background: #343a40;
            text-align: center;
            padding: 10px;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }
        nav a:hover {
            color: #20c997;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #28a745;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 15px;
            font-weight: bold;
        }
        input, textarea {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        textarea {
            resize: vertical;
            height: 120px;
        }
        button {
            margin-top: 20px;
            padding: 12px;
            border: none;
            background: #28a745;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<header>
    <h1>Hotel Lilies - Contact Us</h1>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="rooms.php">Rooms</a>
    <a href="booking.php">Book Now</a>
    <a href="contact.php">Contact</a>
</nav>

<div class="container">
    <h2>We’d love to hear from you</h2>
    <div class="message"><?php echo $message; ?></div>

    <form method="POST" action="">
        <label>Full Name</label>
        <input type="text" name="fullname" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Your Message</label>
        <textarea name="message" required></textarea>

        <button type="submit">Send Message</button>
    </form>
</div>

</body>
</html>
