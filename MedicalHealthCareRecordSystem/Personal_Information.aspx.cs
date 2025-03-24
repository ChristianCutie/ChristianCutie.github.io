using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Data;
using System.Data.SqlClient;
using System.Configuration;

public partial class login : System.Web.UI.Page
{
    string connection = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;

    private UsersChat users = new UsersChat();
    private Patient_List patient_list = new Patient_List();
    protected void Page_Load(object sender, EventArgs e)
    {
        if (Session["usern"] != null && Session["emailAddress"] != null && Session["passWord"] != null)
        {
            lblvisibleusername.Text = Session["usern"].ToString();
            lblvisibleemail.Text = Session["emailAddress"].ToString();
            lblvisiblepassword.Text = Session["passWord"].ToString();
        }
        if (!IsPostBack)
        {
            DateTime today = DateTime.Now;
            //txtbday.Attributes["max"] = today.ToString("yyyy-MM-dd");
            //txtbday.Attributes.Add("placeholder", "Birthday");
        }
       
    }

    protected void btnsubmit_Click(object sender, EventArgs e)
    {
        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {
            if (txtfullname.Text == "" || txtaddress.Text == "" || txtcontact.Text == "" || txtbday.Text == "" || txtnationality.Text == "" || ddlgender.SelectedValue == "" || txtage.Text == "")
            {
                error.Visible = true;
                lblerror.Text = "Please fill the required field.";
            }
            else
            {
                if (checkbox.Checked)
                {
                    if (Session["usern"] != null && Session["emailAddress"] != null && Session["passWord"] != null)
                    {
                        string username = Session["usern"].ToString();
                        string email = Session["emailAddress"].ToString();
                        string PassWord = Session["passWord"].ToString();

                        con.Open();

                        // Insert into LOGIN_DB and get the ID of the inserted record
                        string query2 = @"INSERT INTO LOGIN_DB (USERNAME, EMAIL_ADDRESS, PASSWORD, USERTYPE) 
                                  OUTPUT INSERTED.ID 
                                  VALUES (@username, @email, @password, @usertype)";

                        int loginId = 0;
                        using (SqlCommand cmd = new SqlCommand(query2, con))
                        {
                            cmd.Parameters.AddWithValue("@username", txtfullname.Text);
                            cmd.Parameters.AddWithValue("@password", PassWord);
                            cmd.Parameters.AddWithValue("@email", email);
                            cmd.Parameters.AddWithValue("@usertype", "Patient");
                            loginId = (int)cmd.ExecuteScalar(); // Get the newly inserted ID
                        }

                        // Insert into Patient_List
                        Patient_List list = new Patient_List()
                        {
                            FULLNAME = txtfullname.Text,
                            ADDRESS = txtaddress.Text,
                            CONTACT = txtcontact.Text,
                            BDAY = txtbday.Text,
                            NATIONALITY = txtnationality.Text,
                            GENDER = ddlgender.SelectedItem.Text,
                            AGE = int.Parse(txtage.Text),
                            LOGIN_ID = loginId
                        };
                        patient_list.InsertData(list);

                        UsersChat UserList = new UsersChat()
                        {
                            UserID = loginId.ToString(),
                            UserName = txtfullname.Text,
                            UserType = "Patient",
                            Email = email,
                            Password = PassWord,
                            DateRegistered = DateTime.Now
                        };
                        users.InsertNewChatUser(UserList);

                        con.Close();

                        // Clear session and redirect
                        Session.Abandon();
                        Session.Clear();
                        Session.RemoveAll();
                        Response.Write("<script>alert('Account has been created! Back to login.'); window.location='login.aspx';</script>");
                    }
                    else
                    {
                        Response.Write("<script>alert('Session has expired. Please log in again.'); window.location='login.aspx';</script>");
                    }
                }
                else
                {
                    ScriptManager.RegisterStartupScript(this, GetType(), "Alert", "alert('Please check the box to indicate your agreement with the use of your personal information.');", true);
                }
            }
        }

    }
}