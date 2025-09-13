<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Assistant - Hotel Lilies</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f0f4f8;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        header {
            background: #64b5f6;
            color: white;
            text-align: center;
            padding: 15px;
        }
        header h1 {
            margin: 0;
            font-size: 1.5em;
        }
        header p {
            margin: 5px 0 0;
            font-size: 0.9em;
        }
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: stretch;
            padding: 10px;
        }
        /* Chatbot container */
        #chatbot-widget {
            width: 100%;
            max-width: 450px;
            height: 100%;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: white;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        #chatbot-header {
            background: #2196f3;
            color: white;
            padding: 12px;
            font-weight: bold;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            text-align: center;
            font-size: 1em;
        }
        #chatbot-messages {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #f0f0f0;
        }
        #chatbot-input-container {
            display: flex;
            border-top: 1px solid #ccc;
            background: white;
        }
        #chatbot-input {
            flex: 1;
            padding: 12px;
            border: none;
            font-size: 1em;
        }
        #chatbot-send {
            padding: 12px 18px;
            background: #2196f3;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
        }
        #chatbot-send:hover {
            background: #1976d2;
        }
        /* Messages styling */
        .user-msg {
            align-self: flex-end;
            background: #64b5f6;
            color: white;
            padding: 10px 14px;
            border-radius: 15px;
            max-width: 75%;
            word-wrap: break-word;
        }
        .bot-msg {
            align-self: flex-start;
            background: #e0e0e0;
            padding: 10px 14px;
            border-radius: 15px;
            max-width: 75%;
            word-wrap: break-word;
        }
        /* Mobile adjustments */
        @media (max-width: 600px) {
            header h1 {
                font-size: 1.2em;
            }
            header p {
                font-size: 0.8em;
            }
            #chatbot-widget {
                max-width: 100%;
                height: 100%;
                border-radius: 0;
            }
            #chatbot-header {
                font-size: 0.9em;
                padding: 10px;
            }
            #chatbot-input {
                font-size: 0.9em;
            }
            #chatbot-send {
                font-size: 0.9em;
                padding: 10px 14px;
            }
        }
    </style>
</head>
<body>
<header>
    <button onclick="window.location.href='customer_portal.php'" 
        style="background:#ffffff; color:#2196f3; border:none; padding:8px 14px; 
               font-size:0.9em; border-radius:6px; cursor:pointer; float:left; margin-right:10px;">
        ⬅ Back
    </button>
    <h1 style="margin:0;">Hotel Assistant 🤖</h1>
    <p style="margin:5px 0 0;">Ask about rooms, menu, bookings, and more</p>
</header>


<main>
    <div id="chatbot-widget">
        <div id="chatbot-header">Hotel Assistant</div>
        <div id="chatbot-messages"></div>
        <div id="chatbot-input-container">
            <input type="text" id="chatbot-input" placeholder="Type your message...">
            <button id="chatbot-send">Send</button>
        </div>
    </div>
</main>

<script>
document.getElementById("chatbot-send").addEventListener("click", function () {
    const input = document.getElementById("chatbot-input");
    const messages = document.getElementById("chatbot-messages");
    const userMsg = input.value.trim();
    if (!userMsg) return;

    // Add user message
    const userDiv = document.createElement("div");
    userDiv.className = "user-msg";
    userDiv.innerText = userMsg;
    messages.appendChild(userDiv);

    // Add typing indicator
    const typingDiv = document.createElement("div");
    typingDiv.className = "bot-msg";
    typingDiv.innerText = "Typing...";
    messages.appendChild(typingDiv);

    messages.scrollTop = messages.scrollHeight;
    input.value = "";

    // Send to backend
    fetch("chatbot_backend.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "message=" + encodeURIComponent(userMsg)
    })
    .then(res => res.json())
    .then(data => {
        messages.removeChild(typingDiv); // remove typing indicator
        const botDiv = document.createElement("div");
        botDiv.className = "bot-msg";
        botDiv.innerText = data.reply;
        messages.appendChild(botDiv);
        messages.scrollTop = messages.scrollHeight;
    })
    .catch(() => {
        messages.removeChild(typingDiv);
        const errorDiv = document.createElement("div");
        errorDiv.className = "bot-msg";
        errorDiv.innerText = "Error contacting assistant.";
        messages.appendChild(errorDiv);
    });
});
</script>

</body>
</html>
