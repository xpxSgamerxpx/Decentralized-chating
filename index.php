<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="modern.css">
    <title>Simple Chat Application</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .chat-container {
            width: 500px;
            height: 300px;
            border: 1px solid #ccc;
            padding: 10px;
            overflow-y: scroll;
        }
    </style>
</head>
<body>
    <p><?php include 'code.php'; ?></p>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat</h2>
        </div>
        <div class="chat-messages" id="chatbox"></div>
        <div class="chat-form">
            <input type="text" id="username" placeholder="Enter your name">
            <input type="text" id="messageInput" placeholder="Type message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <div class="chat-container">
        <div class="chat-messages">
            <!-- chat messages go here -->
        </div>
        <form action="sendmessage.php" method="post" class="chat-form">
            <input type="text" name="username" placeholder="Username">
            <input type="text" name="message" placeholder="Message">
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            var socket = new WebSocket("ws://localhost:8080");

            socket.onopen = function() {
                console.log("Connected to server");
            };

            socket.onmessage = function(event) {
                var data = JSON.parse(event.data);
                var message = "<p><strong>" + data.username + ": </strong>" + data.message + "</p>";
                $("#chatbox").append(message);
            };

            $("#send").click(function() {
                var message = $("#messageInput").val();
                socket.send(JSON.stringify({
                    username: "User",
                    message: message
                }));
                $("#messageInput").val("");
            });
        });
    </script>
</body>
</html>
