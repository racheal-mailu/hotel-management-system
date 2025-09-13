<?php
// chatbot.php
?>

<!-- Floating Chatbot Widget -->
<div id="chatbot-widget" style="position:fixed; bottom:60px; right:20px; width:300px; font-family:Arial, sans-serif; border-radius:10px; box-shadow:0 4px 8px rgba(0,0,0,0.2); overflow:hidden; max-height:50px; transition:max-height 0.3s ease; background:#e0f7f1; z-index:1000;">

    <!-- Header -->
    <div id="chatbot-header" style="padding:10px; cursor:pointer; display:flex; align-items:center; justify-content:space-between; background:#009688; color:white; font-weight:bold; border-top-left-radius:10px; border-top-right-radius:10px;">
        <span>Hotel Assistant</span>
        <span style="font-size:16px;">💬</span>
    </div>

    <!-- Collapsible Content -->
    <div id="chatbot-content" style="display:none; flex-direction:column; background:#f0fdf9; border-top:1px solid #b2dfdb;">
        <!-- Messages container -->
        <div id="chatbot-messages" style="flex:1; padding:10px; overflow-y:auto; max-height:250px; display:flex; flex-direction:column; gap:5px;"></div>

        <!-- Quick-reply buttons -->
        <div id="chatbot-quick" style="display:flex; gap:5px; flex-wrap:wrap; padding:5px;">
            <button class="quick-btn" data-msg="Check in">Check-in Info</button>
            <button class="quick-btn" data-msg="Check out">Check-out Info</button>
            <button class="quick-btn" data-msg="Services">Services</button>
            <button class="quick-btn" data-msg="Location">Location</button>
            <button class="quick-btn" data-msg="Available Rooms">Available Rooms</button>
            <button class="quick-btn" data-msg="Menu">Menu</button>
        </div>

        <!-- Input area -->
        <div style="display:flex; padding:10px; gap:5px; border-top:1px solid #b2dfdb;">
            <input id="chatbot-input" type="text" placeholder="Type a message..." style="flex:1; padding:5px; border:1px solid #b2dfdb; border-radius:5px;">
            <button id="chatbot-send" style="background:#009688; color:white; border:none; border-radius:5px; padding:5px 10px; cursor:pointer;">Send</button>
        </div>
    </div>
</div>

<script>
const widget = document.getElementById("chatbot-widget");
const header = document.getElementById("chatbot-header");
const content = document.getElementById("chatbot-content");
const messages = document.getElementById("chatbot-messages");
let isOpen = false;

// Toggle chatbox open/close
header.addEventListener("click", () => {
    if (!isOpen) {
        widget.style.maxHeight = "400px";
        content.style.display = "flex";
        isOpen = true;
    } else {
        widget.style.maxHeight = "50px";
        setTimeout(() => content.style.display = "none", 300);
        isOpen = false;
    }
});

// Function to send a message
function sendMessage(msg) {
    if(!msg.trim()) return;

    // User message
    const userDiv = document.createElement("div");
    userDiv.style.background="#26a69a";
    userDiv.style.color="white";
    userDiv.style.padding="5px 10px";
    userDiv.style.borderRadius="10px";
    userDiv.style.alignSelf="flex-end";
    userDiv.innerText = msg;
    messages.appendChild(userDiv);

    // Typing indicator
    const typingDiv = document.createElement("div");
    typingDiv.style.background="#c8f8f3";
    typingDiv.style.color="#004d40";
    typingDiv.style.padding="5px 10px";
    typingDiv.style.borderRadius="10px";
    typingDiv.style.fontStyle="italic";
    typingDiv.innerText = "Typing...";
    messages.appendChild(typingDiv);

    messages.scrollTop = messages.scrollHeight;

    // Clear input
    document.getElementById("chatbot-input").value = "";

    // Send to backend
    fetch("chatbot_backend.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "message=" + encodeURIComponent(msg)
    })
    .then(res => res.json())
    .then(data => {
        messages.removeChild(typingDiv);

        const botDiv = document.createElement("div");
        botDiv.style.background="#c8f8f3";
        botDiv.style.color="#004d40";
        botDiv.style.padding="5px 10px";
        botDiv.style.borderRadius="10px";
        botDiv.style.alignSelf="flex-start";
        botDiv.innerText = data.reply;
        messages.appendChild(botDiv);
        messages.scrollTop = messages.scrollHeight;
    })
    .catch(() => {
        messages.removeChild(typingDiv);
        const errorDiv = document.createElement("div");
        errorDiv.style.background="#ffcdd2";
        errorDiv.style.color="#b71c1c";
        errorDiv.style.padding="5px 10px";
        errorDiv.style.borderRadius="10px";
        errorDiv.style.alignSelf="flex-start";
        errorDiv.innerText = "Error contacting chatbot.";
        messages.appendChild(errorDiv);
    });
}

// Send button click
document.getElementById("chatbot-send").addEventListener("click", () => {
    const msg = document.getElementById("chatbot-input").value;
    sendMessage(msg);
});

// Quick-reply buttons
document.querySelectorAll(".quick-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        const msg = btn.getAttribute("data-msg");
        sendMessage(msg);
    });
});

// Optional: send message on Enter key
document.getElementById("chatbot-input").addEventListener("keypress", (e) => {
    if(e.key === "Enter") sendMessage(e.target.value);
});
</script>
