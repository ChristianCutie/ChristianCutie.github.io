<%@ Control Language="C#" AutoEventWireup="true" CodeFile="ChatControl.ascx.cs" Inherits="Chat" %>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
<div id="chat-container" class="chat-container hidden">
    <div class="chat-header">
        <span id="chat-title">Chat</span>
        <div>
            <button type="button" id="btn-minimize">_</button>
            <button type="button" id="btn-close"><i class="fa fa-x"></i></button>
        </div>
    </div>
    <div id="chat-body" class="chat-body"></div>
    <div class="chat-footer">
        <input type="text" id="message-input" class="form-control" placeholder="Type a message..." />
        <button type="button" id="btn-send"><i class=" fa-solid fa-paper-plane fa-lg"></i></button>
    </div>
</div>

<!-- User List -->
<div id="user-list" class="user-list hidden"></div>

<!-- Chat Button -->
<button id="btn-open-chat" type="button" style="position: fixed; bottom: 20px; right: 20px; z-index: 999;">
    <i class="fa fa-comment fa-lg"></i>
</button>
 
<script>
    $(function () {
        // Get user info from server
        var userId = '<%= Session["UserId"] %>';
            var userType = '<%= Session["UserType"] %>';
            var userName = '<%= Session["UserName"] %>';
            var selectedUserId = null;

            // Initialize SignalR
            var chatHub = $.connection.chatHub;

            // Client-side methods that server can call
            chatHub.client.receiveMessage = function (senderId, senderType, senderName, message, isMine) {
                var messageClass = isMine ? 'message message-sent' : 'message message-received';
                var senderInfo = isMine ? 'You' : senderName; // Use name instead of type+id

                appendMessage(senderInfo, message, messageClass);
            };

            // Updated method to receive all users (online and offline)
            chatHub.client.getAllUsers = function (users) {
                $('#user-list').empty();

                // Display users with their names
                $.each(users, function (i, user) {
                    var onlineStatus = user.IsOnline ?
                        '<span class="online-indicator"></span>' :
                        '<span class="offline-indicator"></span>';

                    var statusText = user.IsOnline ? 'Online' : 'Offline';

                    $('#user-list').append(
                        '<div class="user-item" data-user-id="' + user.UserId + '">' +
                        onlineStatus +
                        '<strong>' + user.UserName + '</strong>' +
                        '<span class="status-text">' + statusText + '</span>' +
                        '</div>'
                    );
                });
        };
        chatHub.client.userDisconnected = function (userId, userType, userName) {
            // Find the user in the list and update their status
            $('.user-item').each(function () {
                if ($(this).data('user-id') === userId) {
                    // Update the online indicator to show offline status
                    $(this).find('.online-indicator')
                        .removeClass('online-indicator')
                        .addClass('offline-indicator');

                    // Update the status text
                    $(this).find('.status-text').text('Offline');
                }
            });

            // Optional: Display a notification that user has gone offline
            // You could show a temporary message in the chat area or as a toast notification
            var notification = userName || (userType + ' ' + userId);
            appendSystemMessage(notification + ' is now offline');
        };

        function appendSystemMessage(message) {
            $('#chat-body').append(
                '<div class="message message-system">' +
                '<em>' + message + '</em>' +
                '</div>'
            );
            scrollToBottom();
        }
        // Add this client method to handle user connection
        chatHub.client.userConnected = function (userId, userType, userName) {
            // Refresh the entire user list (easier approach)
            chatHub.server.getAllUsers();

            // Or update just this specific user's status (more efficient)
            $('.user-item').each(function () {
                if ($(this).data('user-id') === userId) {
                    // Update the offline indicator to online
                    $(this).find('.offline-indicator')
                        .removeClass('offline-indicator')
                        .addClass('online-indicator');

                    // Update the status text
                    $(this).find('.status-text').text('Online');
                }
            });

            var notification = userName || (userType + ' ' + userId);
            appendSystemMessage(notification + ' is now online');
        };
            // Keep the previous getOnlineUsers method for backward compatibility
            chatHub.client.getOnlineUsers = function (users) {
            };

            // New method to load chat history
            chatHub.client.loadChatHistory = function (messages) {
                // Clear previous chat
                $('#chat-body').empty();

                // Add messages to chat
                $.each(messages, function (i, msg) {
                    var messageClass = msg.IsMine ? 'message message-sent' : 'message message-received';
                    var senderInfo = msg.IsMine ? 'You' : msg.SenderName; // Use name instead of type+id

                    var formattedDate = new Date(msg.SentTime).toLocaleString();

                    appendMessage(senderInfo, msg.Message, messageClass, formattedDate);
                });

                // Scroll to bottom
                scrollToBottom();
            };

            function appendMessage(sender, message, cssClass, timestamp) {
                var timeDisplay = timestamp ? '<small class="text-muted-chat">' + timestamp + '</small><br/>' : '';

                $('#chat-body').append(
                    '<div class="' + cssClass + '">' +
                    '<strong>' + sender + ':</strong> ' + timeDisplay +
                    message +
                    '</div>'
                );

                scrollToBottom();
            }

            function scrollToBottom() {
                $('#chat-body').scrollTop($('#chat-body')[0].scrollHeight);
            }

            // Start the connection
            $.connection.hub.start().done(function () {
                console.log("Connected to SignalR hub");

                // Connect with user info
                chatHub.server.connect(userId, userType, userName);

                // Get all users (including offline ones)
                chatHub.server.getAllUsers();

                // Handle send message button
                $('#btn-send').click(function (e) {
                    e.preventDefault();
                    sendMessage();
                });

                // Handle enter key
                $('#message-input').keypress(function (e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        sendMessage();
                    }
                });

                function sendMessage() {
                    var message = $('#message-input').val();
                    if (message && selectedUserId) {
                        chatHub.server.sendMessage(selectedUserId, message);
                        $('#message-input').val('');
                    }
                }
            });

            // UI Controls
            $('#btn-open-chat').click(function (e) {
                e.preventDefault();
                $('#chat-container').removeClass('hidden');
                $('#user-list').removeClass('hidden');

                // Refresh user list when opening chat
                chatHub.server.getAllUsers();
            });
            $('#btn-close').click(function (e) {
                e.preventDefault();
                $('#chat-container').addClass('hidden');
                $('#user-list').addClass('hidden');
            });
            $('#btn-minimize').click(function (e) {
                e.preventDefault();
                $('#chat-container').addClass('hidden');
                $('#user-list').addClass('hidden');
            });

            // Handle user selection
            $(document).on('click', '.user-item', function () {
                selectedUserId = $(this).data('user-id');
                var selectedUserName = $(this).find('strong').text();

                // Update chat title with name instead of type+id
                $('#chat-title').text('Chat with ' + selectedUserName);

                // Load chat history
                chatHub.server.getChatHistory(selectedUserId);

                // Focus on input
                $('#message-input').focus();
            });
        });
</script>