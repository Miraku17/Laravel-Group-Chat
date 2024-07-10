<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Group Chat</title>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .chat-container {
            max-width: 600px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        h1 {
            background-color: #4a76a8;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }

        .message-list {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .message {
            background-color: #e9ebee;
            border-radius: 18px;
            padding: 10px 15px;
            margin-bottom: 10px;
            max-width: 80%;
            align-self: flex-start;
        }

        .message strong {
            color: #4a76a8;
            margin-right: 5px;
        }

        .input-container {
            display: flex;
            padding: 20px;
            background-color: #f5f6f7;
        }

        input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #dddfe2;
            border-radius: 20px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #4a76a8;
        }

        button {
            background-color: #4a76a8;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            margin-left: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #3a5a78;
        }
    </style>
</head>

<body>
    <div id="app" class="chat-container">
        <h1>Laravel Group Chat</h1>
        <div class="message-list">
            <div v-for="message in reversedMessages" :key="message.id" class="message">
                <strong>@{{ message.username }}:</strong> @{{ message.message }}
            </div>
        </div>
        <div class="input-container">
            <input v-model="username" placeholder="Your name" @keyup.enter="sendMessage">
            <input v-model="newMessage" placeholder="Type a message" @keyup.enter="sendMessage">
            <button @click="sendMessage">Send</button>
        </div>
    </div>

    <script>
        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true
        });

        new Vue({
            el: '#app',
            data: {
                messages: [],
                newMessage: '',
                username: '',
            },
            computed: {
                reversedMessages() {
                    return [...this.messages].reverse();
                }
            },
            mounted() {
                this.getMessages();

                Echo.channel('chat')
                    .listen('MessageSent', (e) => {
                        this.messages.push(e.message);
                        this.scrollToBottom();
                    });
            },
            methods: {
                getMessages() {
                    axios.get('/get-messages')
                        .then(response => {
                            this.messages = response.data;
                        })
                        .catch(error => {
                            console.error('Error fetching messages:', error);
                        });
                },
                sendMessage() {
                    if (this.newMessage.trim() === '' || this.username.trim() === '') return;

                    axios.post('/send-message', {
                            username: this.username,
                            message: this.newMessage
                        })
                        .then(response => {
                            this.newMessage = '';
                            this.getMessages(); // Refresh messages after sending
                        })
                        .catch(error => {
                            console.error('Error sending message:', error);
                        });
                }
            }
        });
    </script>
</body>

</html>
