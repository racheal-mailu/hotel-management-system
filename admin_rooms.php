<?php
session_start();
include('db_connect.php');

$message = "";

// Handle Add Room form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_room'])) {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price_per_night = $_POST['price_per_night'];
    $status = $_POST['status'];

    $sql = "INSERT INTO rooms (room_number, room_type, price_per_night, status)
            VALUES ('$room_number', '$room_type', '$price_per_night', '$status')";

    if (mysqli_query($conn, $sql)) {
        $message = "✅ Room added successfully!";
    } else {
        $message = "❌ Error: " . mysqli_error($conn);
    }
}

// Handle room deletion via GET
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM rooms WHERE room_id = $delete_id";
    if (mysqli_query($conn, $delete_sql)) {
        echo "<script>alert('Room deleted successfully'); window.location.href='admin_rooms.php';</script>";
        exit;
    } else {
        echo "Error deleting room: " . mysqli_error($conn);
    }
}

// Fetch all rooms
$sql_rooms = "SELECT * FROM rooms ORDER BY room_number ASC";
$rooms_result = mysqli_query($conn, $sql_rooms);

if (!$rooms_result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Rooms - Hotel Management</title>
<style>
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
    th { background-color: #f2f2f2; }
    .btn { padding: 5px 10px; text-decoration: none; color: white; border: none; cursor: pointer; }
    .add-btn { background-color: green; }
    .edit-btn { background-color: orange; }
    .delete-btn { background-color: red; }
    form { margin-top: 20px; padding: 15px; border: 1px solid #ccc; width: 300px; }
    form input, form select { width: 100%; padding: 5px; margin-bottom: 10px; }

    /* Modal styles */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; 
             overflow: auto; background-color: rgba(0,0,0,0.5); }
    .modal-content { background-color: #fff; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 300px; }
    .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
    .close:hover { color: black; }
</style>
</head>
<body>
<h1>Manage Rooms</h1>

<?php if (!empty($message)) echo "<p>$message</p>"; ?>

<!-- Add Room Form -->
<h2>Add New Room</h2>
<form method="POST" action="">
    <input type="hidden" name="add_room" value="1">
    <label>Room Number:</label>
    <input type="text" name="room_number" required>

    <label>Room Type:</label>
    <select name="room_type" required>
        <option value="Single">Single</option>
        <option value="Double">Double</option>
        <option value="Suite">Suite</option>
    </select>

    <label>Price per Night:</label>
    <input type="number" step="0.01" name="price_per_night" required>

    <label>Status:</label>
    <select name="status" required>
        <option value="Available">Available</option>
        <option value="Occupied">Occupied</option>
        <option value="Maintenance">Maintenance</option>
    </select>

    <button type="submit">Add Room</button>
</form>

<!-- Rooms List -->
<?php if (mysqli_num_rows($rooms_result) > 0): ?>
<table id="roomsTable">
    <tr>
        <th>Room ID</th>
        <th>Room Number</th>
        <th>Room Type</th>
        <th>Price per Night</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($room = mysqli_fetch_assoc($rooms_result)): ?>
    <tr id="roomRow<?php echo $room['room_id']; ?>">
        <td><?php echo $room['room_id']; ?></td>
        <td class="room_number"><?php echo htmlspecialchars($room['room_number']); ?></td>
        <td class="room_type"><?php echo htmlspecialchars($room['room_type']); ?></td>
        <td class="price_per_night"><?php echo htmlspecialchars($room['price_per_night']); ?></td>
        <td class="status"><?php echo htmlspecialchars($room['status']); ?></td>
        <td>
            <button class="btn edit-btn" 
                onclick="openEditModal(
                    <?php echo $room['room_id']; ?>,
                    '<?php echo htmlspecialchars($room['room_number']); ?>',
                    '<?php echo htmlspecialchars($room['room_type']); ?>',
                    '<?php echo $room['price_per_night']; ?>',
                    '<?php echo $room['status']; ?>'
                )">Edit</button>
            <button class="btn delete-btn" onclick="confirmDelete(<?php echo $room['room_id']; ?>)">Delete</button>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p>No rooms found.</p>
<?php endif; ?>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Edit Room</h2>
        <form id="editRoomForm">
            <input type="hidden" id="edit_room_id" name="room_id">

            <label>Room Number:</label>
            <input type="text" id="edit_room_number" name="room_number" required>

            <label>Room Type:</label>
            <input type="text" id="edit_room_type" name="room_type" required>

            <label>Price per Night:</label>
            <input type="number" step="0.01" id="edit_price_per_night" name="price_per_night" required>

            <label>Status:</label>
            <select id="edit_status" name="status" required>
                <option value="Available">Available</option>
                <option value="Occupied">Occupied</option>
                <option value="Maintenance">Maintenance</option>
            </select>

            <button type="submit">Update Room</button>
        </form>
        <p id="editMessage"></p>
    </div>
</div>

<script>
function confirmDelete(roomId) {
    if (confirm("Are you sure you want to delete this room?")) {
        window.location.href = "admin_rooms.php?delete_id=" + roomId;
    }
}

function openEditModal(roomId, roomNumber, roomType, price, status) {
    document.getElementById('edit_room_id').value = roomId;
    document.getElementById('edit_room_number').value = roomNumber;
    document.getElementById('edit_room_type').value = roomType;
    document.getElementById('edit_price_per_night').value = price;
    document.getElementById('edit_status').value = status;
    document.getElementById('editMessage').innerText = '';
    document.getElementById('editModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

// AJAX for edit
document.getElementById('editRoomForm').addEventListener('submit', function(e){
    e.preventDefault();
    let formData = new FormData(this);

    fetch('update_room_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        let msg = document.getElementById('editMessage');
        if(data.success){
            msg.innerText = "✅ Room updated successfully!";
            // Update the table row
            let row = document.getElementById('roomRow' + formData.get('room_id'));
            row.querySelector('.room_number').innerText = formData.get('room_number');
            row.querySelector('.room_type').innerText = formData.get('room_type');
            row.querySelector('.price_per_night').innerText = formData.get('price_per_night');
            row.querySelector('.status').innerText = formData.get('status');

            setTimeout(() => { closeModal(); }, 1000);
        } else {
            msg.innerText = "❌ " + data.message;
        }
    })
    .catch(err => console.error(err));
});
</script>
</body>
</html>
