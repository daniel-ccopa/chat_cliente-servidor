<!DOCTYPE html>
<html>
<head>
    <title>Chat de Programadores</title>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #012340; /* Fondo oscuro */
            margin: 0;
            padding: 0;
            color: #ffffff; /* Texto blanco */
        }

        #chat-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #025939;
            flex-direction: column;
            height: 100vh;
            border: 3px solid #d9f1fa; /* Borde para destacar el contenedor */
            position: relative;
            /* Imagen de fondo */
            background-image: url('public/images/fondochat.jpg'); /* Reemplaza con la ruta de tu imagen */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        #chat-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(30, 30, 30, 0.95); /* Overlay semitransparente para oscurecer la imagen de fondo */
            display: flex;
            flex-direction: column;
        }

        #chat-header {
            background-color: #007acc; /* Azul oscuro */
            color: white;
            padding: 15px;
            text-align: center;
            display: flex;
            align-items: center;
        }

        #chat-header h2 {
            flex: 1;
            margin: 0;
        }

        #chat {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            background-color: transparent; /* Transparente para mostrar el fondo */
        }

        .message {
            display: block;
            margin: 10px 0;
            max-width: 80%;
            padding: 10px;
            border-radius: 10px;
            clear: both;
            word-wrap: break-word;
            color: #d4d4d4; /* Texto gris claro */
        }

        .message.sent {
            background-color: #0e639c; /* Azul oscuro para mensajes enviados */
            margin-left: auto;
            text-align: right;
        }

        .message.received {
            background-color: #3c3c3c; /* Gris oscuro para mensajes recibidos */
            margin-right: auto;
            text-align: left;
        }

        #chat-footer {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #2d2d30; /* Fondo oscuro para el footer */
            position: relative;
        }

        #emoji-button,
        #attach-button,
        #camera-button {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            margin-right: 10px;
            color: #d4d4d4; /* Color de los íconos */
        }

        #message {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 20px;
            outline: none;
            background-color: #3c3c3c; /* Fondo del input */
            color: #ffffff; /* Texto del input */
        }

        #send {
            background-color: #0e639c; /* Azul oscuro */
            border: none;
            color: white;
            padding: 10px;
            margin-left: 10px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 24px;
        }

        #send:hover {
            background-color: #1177bb;
        }

        #username-container {
            padding: 10px;
            background-color: #2d2d30; /* Fondo oscuro */
            text-align: center;
        }

        #username {
            padding: 10px;
            border: none;
            border-radius: 20px;
            outline: none;
            width: 50%;
            background-color: #3c3c3c;
            color: #ffffff;
        }

        #set-username {
            background-color: #007acc;
            border: none;
            color: white;
            padding: 10px 20px;
            margin-left: 10px;
            border-radius: 20px;
            cursor: pointer;
        }

        #set-username:hover {
            background-color: #1177bb;
        }

        /* Estilos para el emoji picker */
        #emoji-picker {
            position: absolute;
            bottom: 60px;
            left: 10px;
            background: #252526; /* Fondo oscuro */
            border: 1px solid #3c3c3c;
            padding: 5px;
            display: none;
            max-width: 300px;
            overflow-y: auto;
            max-height: 200px;
            z-index: 1000;
        }

        #emoji-picker span {
            font-size: 24px;
            cursor: pointer;
            padding: 5px;
        }

        /* Estilos para previsualización de imágenes */
        .message img {
            max-width: 100%;
            border-radius: 10px;
        }

        /* Estilos para enlaces de descarga */
        .download-link {
            color: #569cd6;
            text-decoration: none;
        }

        .download-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div id="chat-container">
        <div id="chat-overlay">
            <div id="chat-header">
                <h2>Chat de Programadores</h2>
            </div>

            <div id="username-container">
                <input type="text" id="username" placeholder="Ingresa tu nombre de usuario">
                <button id="set-username">Entrar al Chat</button>
            </div>

            <div id="chat" style="display: none;"></div>

            <div id="chat-footer" style="display: none;">
                <button id="emoji-button">&#x1F600;</button>
                <button id="attach-button">&#x1F4CE;</button>
                <button id="camera-button">&#x1F4F7;</button>
                <input type="text" id="message" placeholder="Escribe tu mensaje">
                <button id="send">&#x27A4;</button>
                <div id="emoji-picker"></div>
                <input type="file" id="file-input" style="display: none;" accept="*/*">
            </div>
        </div>
    </div>

    <script>
        var conn;
        var username = '';
        var chat = document.getElementById('chat');
        var sendButton = document.getElementById('send');
        var messageInput = document.getElementById('message');
        var usernameInput = document.getElementById('username');
        var setUsernameButton = document.getElementById('set-username');
        var chatFooter = document.getElementById('chat-footer');
        var usernameContainer = document.getElementById('username-container');
        var emojiButton = document.getElementById('emoji-button');
        var emojiPicker = document.getElementById('emoji-picker');
        var attachButton = document.getElementById('attach-button');
        var cameraButton = document.getElementById('camera-button');
        var fileInput = document.getElementById('file-input');

        // Lista básica de emojis
        var emojis = ['😀','😁','😂','🤣','😃','😄','😅','😆','😉','😊','😋','😎','😍','😘','🥰','😗','😙','😚','🙂','🤗','🤩','🤔','🤨','😐','😑','😶','🙄','😏','😣','😥','😮','🤐','😯','😪','😫','🥱','😴'];

        setUsernameButton.onclick = function() {
            username = usernameInput.value.trim();
            if (username !== '') {
                usernameContainer.style.display = 'none';
                chat.style.display = 'block';
                chatFooter.style.display = 'flex';
                iniciarConexion();
            } else {
                alert('Por favor, ingresa un nombre de usuario.');
            }
        };

        function iniciarConexion() {
            conn = new WebSocket('ws://localhost:8081');
            conn.onopen = function(e) {
                chat.innerHTML += '<div class="message received">Conexión establecida</div>';
            };

            conn.onmessage = function(e) {
                var data = JSON.parse(e.data);
                mostrarMensaje(data);
            };
        }

        function mostrarMensaje(data) {
            var messageElement = document.createElement('div');
            messageElement.classList.add('message');
            if (data.username === username) {
                messageElement.classList.add('sent');
            } else {
                messageElement.classList.add('received');
            }

            if (data.type === 'text') {
                messageElement.innerHTML = '<strong>' + data.username + ':</strong> ' + data.message;
            } else if (data.type === 'image') {
                messageElement.innerHTML = '<strong>' + data.username + ':</strong><br><img src="' + data.message + '">';
            } else if (data.type === 'file') {
                // Si el archivo es una imagen, mostrarla
                if (data.filetype.startsWith('image/')) {
                    messageElement.innerHTML = '<strong>' + data.username + ':</strong><br><img src="' + data.message + '">';
                } else {
                    // Para otros tipos de archivos, mostrar un enlace de descarga
                    var link = document.createElement('a');
                    link.href = data.message;
                    link.download = data.filename;
                    link.textContent = data.filename;
                    link.classList.add('download-link');
                    messageElement.innerHTML = '<strong>' + data.username + ':</strong><br>';
                    messageElement.appendChild(link);
                }
            }
            chat.appendChild(messageElement);
            chat.scrollTop = chat.scrollHeight;
        }

        sendButton.onclick = sendMessage;

        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        function sendMessage() {
            var msg = messageInput.value.trim();
            if (msg !== '') {
                var data = {
                    username: username,
                    message: msg,
                    type: 'text'
                };
                conn.send(JSON.stringify(data));
                messageInput.value = '';
            }
        }

        // Implementación del emoji picker
        emojiButton.onclick = function() {
            if (emojiPicker.style.display === 'block') {
                emojiPicker.style.display = 'none';
            } else {
                emojiPicker.style.display = 'block';
                cargarEmojis();
            }
        };

        function cargarEmojis() {
            emojiPicker.innerHTML = '';
            emojis.forEach(function(emoji) {
                var emojiElement = document.createElement('span');
                emojiElement.textContent = emoji;
                emojiElement.onclick = function() {
                    messageInput.value += emoji;
                    emojiPicker.style.display = 'none';
                    messageInput.focus();
                };
                emojiPicker.appendChild(emojiElement);
            });
        }

        // Envío de archivos
        attachButton.onclick = function() {
            fileInput.click();
        };

        fileInput.onchange = function() {
            var file = fileInput.files[0];
            if (file) {
                // Validar el tamaño del archivo (por ejemplo, máximo 5 MB)
                var maxSize = 5 * 1024 * 1024; // 5 MB
                if (file.size > maxSize) {
                    alert('El archivo es demasiado grande. Tamaño máximo: 5 MB.');
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(e) {
                    var data = {
                        username: username,
                        filename: file.name,
                        filetype: file.type,
                        message: e.target.result,
                        type: 'file' // Tipo 'file' para archivos
                    };
                    conn.send(JSON.stringify(data));
                };
                reader.readAsDataURL(file);
            }
        };

        // Uso de la cámara
        cameraButton.onclick = function() {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function(stream) {
                        var video = document.createElement('video');
                        video.srcObject = stream;
                        video.play();

                        var modal = document.createElement('div');
                        modal.style.position = 'fixed';
                        modal.style.top = '0';
                        modal.style.left = '0';
                        modal.style.width = '100%';
                        modal.style.height = '100%';
                        modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
                        modal.style.display = 'flex';
                        modal.style.alignItems = 'center';
                        modal.style.justifyContent = 'center';

                        var captureButton = document.createElement('button');
                        captureButton.textContent = 'Tomar Foto';
                        captureButton.style.position = 'absolute';
                        captureButton.style.bottom = '20px';
                        captureButton.style.left = '50%';
                        captureButton.style.transform = 'translateX(-50%)';
                        captureButton.style.padding = '10px 20px';
                        captureButton.style.fontSize = '16px';

                        modal.appendChild(video);
                        modal.appendChild(captureButton);
                        document.body.appendChild(modal);

                        captureButton.onclick = function() {
                            var canvas = document.createElement('canvas');
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            canvas.getContext('2d').drawImage(video, 0, 0);
                            var imageData = canvas.toDataURL('image/png');

                            var data = {
                                username: username,
                                message: imageData,
                                type: 'image'
                            };
                            conn.send(JSON.stringify(data));

                            // Detener la transmisión y cerrar el modal
                            stream.getTracks().forEach(function(track) {
                                track.stop();
                            });
                            document.body.removeChild(modal);
                        };
                    })
                    .catch(function(err) {
                        alert('Error al acceder a la cámara: ' + err);
                    });
            } else {
                alert('Tu navegador no soporta acceso a la cámara.');
            }
        };
    </script>
</body>
</html>
