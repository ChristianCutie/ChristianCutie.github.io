using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data.SqlClient;
using System.Linq;
using System.Web;

/// <summary>
/// Summary description for UsersChat
/// </summary>
public class UsersChat
{
    public string UserID { get; set; }
    public string UserName { get; set; }
    public string UserType { get; set; }
    public string Password { get; set; }
    public string Email { get; set; }
    public DateTime DateRegistered { get; set; }


    public void InsertNewChatUser(UsersChat uc)
    {
        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {

            string query = @"INSERT INTO Users (UserID, UserName, UserType, Password, Email, DateRegistered) VALUES 
                    (@uid, @uname, @utype, @pass, @email, @dr)";

            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                cmd.Parameters.AddWithValue("@uid", uc.UserID);
                cmd.Parameters.AddWithValue("@uname", uc.UserName);
                cmd.Parameters.AddWithValue("@utype", uc.UserType);
                cmd.Parameters.AddWithValue("@pass", uc.Password);
                cmd.Parameters.AddWithValue("@email", uc.Email);
                cmd.Parameters.AddWithValue("@dr", uc.DateRegistered);
                con.Open();
                cmd.ExecuteNonQuery();
                con.Close();
            }
        }
    }
}