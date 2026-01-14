using Microsoft.AspNet.SignalR;
using System.Threading.Tasks;
using System.Collections.Generic;
using System.Linq;
using System.Data.SqlClient;
using System;
using System.Configuration;

public class ChatHub : Hub
{
    // Static dictionary to store user connections
    private static Dictionary<string, UserConnection> _connections = new Dictionary<string, UserConnection>();

    // User connection class to store information
    public class UserConnection
    {
        public string ConnectionId { get; set; }
        public string UserId { get; set; }
        public string UserType { get; set; }
        public string UserName { get; set; }
    }

    // Connect user and store their info
    public void Connect(string userId, string userType, string userName)
    {
        string connectionId = Context.ConnectionId;

        lock (_connections)  // Add a lock here
        {
            if (!_connections.ContainsKey(userId))
            {
                _connections.Add(userId, new UserConnection
                {
                    ConnectionId = connectionId,
                    UserId = userId,
                    UserType = userType,
                    UserName = userName
                });

                // Store connection id mapping
                Groups.Add(connectionId, userId);

                // Notify others that user is online
                Clients.Others.userConnected(userId, userType, userName);
            }
            else
            {
                // Update the connection ID if user reconnects
                _connections[userId].ConnectionId = connectionId;
                _connections[userId].UserName = userName;
                Groups.Add(connectionId, userId);
            }
        }

        // Send the list of online users to the newly connected user
        SendUserList();
    }

    // Get user info based on connection ID
    private UserConnection GetUserInfo(string connectionId)
    {
        lock (_connections)  
        {
            var connections = _connections.Values.ToList();
            return connections.FirstOrDefault(x => x.ConnectionId == connectionId);
        }
    }

    private UserConnection GetUserInfoByUserId(string userId)
    {
        lock (_connections)  // Add a lock to prevent concurrent modification
        {
            var connections = _connections.Values.ToList();
            return connections.FirstOrDefault(x => x.UserId == userId);
        }
    }

    // Get connection ID for a user
    private string GetConnectionId(string userId)
    {
        var userInfo = GetUserInfoByUserId(userId);
        if (userInfo != null)
        {
            return userInfo.ConnectionId;
        }
        return null;
    }

    // Send the current list of online users to the caller
    private void SendUserList()
    {
        var users = _connections.Values.Select(u => new {
            UserId = u.UserId,
            UserType = u.UserType,
            UserName = u.UserName
        }).ToList();

        Clients.Caller.getOnlineUsers(users);
    }

    // Handle disconnection
    public override Task OnDisconnected(bool stopCalled)
    {
        var userConnection = GetUserInfo(Context.ConnectionId);

        if (userConnection != null)
        {
            string userId = userConnection.UserId;
            string userType = userConnection.UserType;
            string userName = userConnection.UserName; 

            lock (_connections)
            {
                _connections.Remove(userId);
            }

            Clients.Others.userDisconnected(userId, userType, userName);
        }

        return base.OnDisconnected(stopCalled);
    }
    // Send a message
    public void SendMessage(string receiverId, string message)
    {
        var senderConnection = GetUserInfo(Context.ConnectionId);

        if (senderConnection != null)
        {
            string senderId = senderConnection.UserId;
            string senderType = senderConnection.UserType;
            string senderName = senderConnection.UserName;

            // Save message to database
            SaveMessageToDatabase(senderId, receiverId, senderType, senderName, message);

            // Get the receiver's connection ID
            string receiverConnectionId = GetConnectionId(receiverId);

            // Send message to receiver if online
            if (!string.IsNullOrEmpty(receiverConnectionId))
            {
                Clients.Client(receiverConnectionId).receiveMessage(senderId, senderType, senderName, message, false);
            }

            // Send confirmation to sender
            Clients.Caller.receiveMessage(senderId, senderType, senderName, message, true);
        }
    }

    // Save message to database
    private void SaveMessageToDatabase(string senderId, string receiverId, string senderType, string senderName, string message)
    {
        string connectionString = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;

        using (SqlConnection connection = new SqlConnection(connectionString))
        {
            // Update your SQL query to include the SenderName column
            string query = @"INSERT INTO CHATMESSAGE_TB (SenderID, ReceiverID, SenderType, SenderName, Message, SentTime, IsRead) 
                         VALUES (@SenderID, @ReceiverID, @SenderType, @SenderName, @Message, GETDATE(), 0)";

            using (SqlCommand command = new SqlCommand(query, connection))
            {
                command.Parameters.AddWithValue("@SenderID", senderId);
                command.Parameters.AddWithValue("@ReceiverID", receiverId);
                command.Parameters.AddWithValue("@SenderType", senderType);
                command.Parameters.AddWithValue("@SenderName", senderName);
                command.Parameters.AddWithValue("@Message", message);

                connection.Open();
                command.ExecuteNonQuery();
            }
        }
    }

    // Get chat history
    public void GetChatHistory(string otherUserId)
    {
        var userConnection = GetUserInfo(Context.ConnectionId);

        if (userConnection != null)
        {
            string userId = userConnection.UserId;

            List<ChatMessage> messages = GetMessagesFromDatabase(userId, otherUserId);

            // Send history to caller
            Clients.Caller.loadChatHistory(messages);

            // Mark messages as read
            MarkMessagesAsRead(userId, otherUserId);
        }
    }

    // Get messages from database
    private List<ChatMessage> GetMessagesFromDatabase(string userId1, string userId2)
    {
        List<ChatMessage> messages = new List<ChatMessage>();
        string connectionString = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;

        using (SqlConnection connection = new SqlConnection(connectionString))
        {
            // Update this SQL query to include the SenderName column
            string query = @"SELECT SenderID, ReceiverID, SenderType, SenderName, Message, SentTime 
                        FROM CHATMESSAGE_TB 
                        WHERE (SenderID = @UserID1 AND ReceiverID = @UserID2) 
                           OR (SenderID = @UserID2 AND ReceiverID = @UserID1) 
                        ORDER BY SentTime ASC";

            using (SqlCommand command = new SqlCommand(query, connection))
            {
                command.Parameters.AddWithValue("@UserID1", userId1);
                command.Parameters.AddWithValue("@UserID2", userId2);

                connection.Open();
                using (SqlDataReader reader = command.ExecuteReader())
                {
                    while (reader.Read())
                    {
                        messages.Add(new ChatMessage
                        {
                            SenderId = reader["SenderID"].ToString(),
                            SenderType = reader["SenderType"].ToString(),
                            SenderName = reader["SenderName"].ToString(),
                            Message = reader["Message"].ToString(),
                            SentTime = Convert.ToDateTime(reader["SentTime"]),
                            IsMine = reader["SenderID"].ToString() == userId1
                        });
                    }
                }
            }
        }

        return messages;
    }

    // Mark messages as read
    private void MarkMessagesAsRead(string userId, string otherUserId)
    {
        string connectionString = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;

        using (SqlConnection connection = new SqlConnection(connectionString))
        {
            string query = @"UPDATE CHATMESSAGE_TB 
                            SET IsRead = 1 
                            WHERE ReceiverID = @UserID AND SenderID = @OtherUserID";

            using (SqlCommand command = new SqlCommand(query, connection))
            {
                command.Parameters.AddWithValue("@UserID", userId);
                command.Parameters.AddWithValue("@OtherUserID", otherUserId);

                connection.Open();
                command.ExecuteNonQuery();
            }
        }
    }
    // Add this method to your ChatHub class
    public void GetAllUsers()
    {
        var currentConnection = GetUserInfo(Context.ConnectionId);
        if (currentConnection == null) return;

        string userType = currentConnection.UserType;
        string userId = currentConnection.UserId;

        // Get online users from our connections dictionary
        var onlineUsers = _connections.Values.Select(u => new {
            UserId = u.UserId,
            UserType = u.UserType,
            UserName = u.UserName,
            IsOnline = true
        }).ToList();

        // For admin, get all users from database (including offline ones)
        if (userType == "Admin" || userType == "Doctor")
        {
            List<object> allUsers = new List<object>();
            string connectionString = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;

            using (SqlConnection connection = new SqlConnection(connectionString))
            {
                // Update this SQL query to include the name field from your Users table
                string query = "SELECT UserId, UserType, UserName FROM Users WHERE UserId <> @CurrentUserId";

                using (SqlCommand command = new SqlCommand(query, connection))
                {
                    command.Parameters.AddWithValue("@CurrentUserId", userId);

                    connection.Open();
                    using (SqlDataReader reader = command.ExecuteReader())
                    {
                        while (reader.Read())
                        {
                            string dbUserId = reader["UserId"].ToString();
                            string dbUserType = reader["UserType"].ToString();
                            string dbUserName = reader["UserName"].ToString();

                            // Check if user is in the online list
                            bool isOnline = onlineUsers.Any(u => u.UserId == dbUserId);

                            allUsers.Add(new
                            {
                                UserId = dbUserId,
                                UserType = dbUserType,
                                UserName = dbUserName,
                                IsOnline = isOnline
                            });
                        }
                    }
                }
            }

            // Send the complete list to caller
            Clients.Caller.getAllUsers(allUsers);
        }
        else
        {
            // For non-admin users, just send online users based on their access rules
            var filteredUsers = onlineUsers.Where(u => u.UserId != userId);

            // Patients can only see doctors and admins
            if (userType == "Patient")
            {
                filteredUsers = filteredUsers.Where(u =>
                    u.UserType == "Doctor" || u.UserType == "Admin");
            }

            Clients.Caller.getAllUsers(filteredUsers);
        }
    }
}

// Chat message class
public class ChatMessage
{
    public string SenderId { get; set; }
    public string SenderType { get; set; }
    public string SenderName { get; set; }
    public string Message { get; set; }
    public DateTime SentTime { get; set; }
    public bool IsMine { get; set; }
}
