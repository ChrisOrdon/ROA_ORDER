// Abre y cierra la ventana del chatbot
function openChat() {
    var chatbox = document.getElementById("chatbox");
    chatbox.style.display = chatbox.style.display === "none" ? "block" : "none";
}

// Envía el mensaje del usuario y obtiene la respuesta
function sendMessage(event) {
    if (event.keyCode === 13 || !event.keyCode) { // Enter key
        var userMessage = document.getElementById("userMessage").value;
        if (userMessage.trim()) {
            appendMessage(userMessage, 'user');
            document.getElementById("userMessage").value = '';  // Clear input field
            getBotResponse(userMessage);  // Get bot's response
        }
    }
}

// Muestra el mensaje en la interfaz
function appendMessage(message, sender) {
    var chatlogs = document.getElementById("chatlogs");
    var messageElement = document.createElement("div");
    messageElement.className = sender;
    messageElement.innerText = message;
    chatlogs.appendChild(messageElement);
    chatlogs.scrollTop = chatlogs.scrollHeight;  // Scroll to the bottom
}

// Obtiene la respuesta del bot desde el servidor
function getBotResponse(userMessage) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "chatbot/chatbot.php", true);  // Actualizado para que apunte al archivo correcto
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            appendMessage(xhr.responseText, 'bot');
        }
    };
    xhr.send("user_message=" + encodeURIComponent(userMessage));
}
