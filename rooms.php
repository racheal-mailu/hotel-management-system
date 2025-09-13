<?php 
include('db_connect.php');

// Fetch all rooms ordered by type and number
$rooms_query = "SELECT * FROM rooms ORDER BY room_type, room_number";
$rooms_result = mysqli_query($conn, $rooms_query);

$rooms_by_type = [];
while($row = mysqli_fetch_assoc($rooms_result)) {
    $rooms_by_type[$row['room_type']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hotel Lilies - Rooms</title>
<style>
body {font-family: Arial;background: #f4f7fa;margin:0;padding:0;}
header {background:#007bff;color:white;text-align:center;padding:15px;}
.room-container {display:grid;grid-template-columns:repeat(auto-fit,minmax(350px,1fr));gap:20px;padding:30px;}
.room {background:white;border-radius:10px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
.slider {position:relative;overflow:hidden;border-radius:8px;height:220px;}
.slides {display:flex;transition:transform 0.5s ease-in-out;height:100%;}
.slides img {min-width:100%;height:100%;object-fit:cover;flex-shrink:0;}
.prev, .next {position:absolute;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:white;border:none;padding:5px 10px;cursor:pointer;border-radius:50%;}
.prev {left:10px;} .next {right:10px;}
.room h3 {margin:10px 0;color:#333;}
.room p {color:#555;}
.book-btn {display:inline-block;margin-top:10px;background:#007bff;color:white;padding:8px 15px;border-radius:5px;text-decoration:none;}
.book-btn:hover {background:#0056b3;}
.select-room {margin-top:10px;padding:5px;width:100%;}
.center {text-align:center;margin-top:20px;}
</style>
</head>
<body>
<header>
    <h1>Our Rooms</h1>
    <p>🛏️ Choose your perfect stay at Hotel Lilies</p>
</header>

<div class="room-container">
<?php foreach($rooms_by_type as $type => $rooms): ?>
    <div class="room">
        <h3><?php echo ucfirst($type); ?></h3>
        <div class="slider" id="<?php echo strtolower($type); ?>-slider">
            <div class="slides">
                <?php
                $images = glob("uploads/$type/*.{jpg,png,JPG,PNG}", GLOB_BRACE);
                if($images){
                    foreach($images as $img){
                        echo "<img src='$img' alt='$type room'>";
                    }
                } else {
                    echo "<img src='uploads/default.png' alt='default room'>";
                }
                ?>
            </div>
            <?php if($images && count($images) > 1): ?>
                <button class="prev" onclick="moveSlide('<?php echo strtolower($type); ?>', -1)">❮</button>
                <button class="next" onclick="moveSlide('<?php echo strtolower($type); ?>', 1)">❯</button>
            <?php endif; ?>
        </div>
        <p>Price: Ksh <?php echo $rooms[0]['price_per_night']; ?> / night</p>
        <p>Status: Available</p>
        <select class="select-room" id="select-<?php echo strtolower($type); ?>">
            <?php foreach($rooms as $room): ?>
                <option value="<?php echo $room['room_id']; ?>" <?php echo ($room['status'] != 'Available') ? 'disabled' : ''; ?>>
                    <?php echo $type . " (Room " . $room['room_number'] . ")" . (($room['status'] != 'Available') ? " - ".$room['status'] : ""); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <a href="#" class="book-btn" onclick="pickRoom('<?php echo strtolower($type); ?>')">Pick This Room</a>
    </div>
<?php endforeach; ?>
</div>

<div class="center">
    <a href="customer_portal.php" class="book-btn">⬅ Back to Customer Portal</a>
</div>

<script>
let slideIndex = {};
function showSlides(type, n){
    const slider = document.getElementById(type+'-slider');
    if(!slider) return;
    const slides = slider.getElementsByTagName('img');
    if(!slideIndex[type]) slideIndex[type]=0;
    if(n!==undefined) slideIndex[type]=n;
    if(slideIndex[type]>=slides.length) slideIndex[type]=0;
    if(slideIndex[type]<0) slideIndex[type]=slides.length-1;
    for(let i=0;i<slides.length;i++) slides[i].style.display='none';
    if(slides.length>0) slides[slideIndex[type]].style.display='block';
}
function moveSlide(type, step){ showSlides(type, slideIndex[type]+step); }
document.addEventListener('DOMContentLoaded', ()=>{
    <?php foreach($rooms_by_type as $type=>$r): ?>
    showSlides('<?php echo strtolower($type); ?>',0);
    <?php endforeach; ?>
});

function pickRoom(type){
    const select = document.getElementById('select-'+type);
    const room_id = select.value;
    window.location.href = "add_bookings.php?room_id="+room_id;
}
</script>
</body>
</html>
