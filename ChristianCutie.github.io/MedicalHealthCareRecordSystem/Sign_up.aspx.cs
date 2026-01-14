using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Data;
using System.Data.SqlClient;
using System.Configuration;
using System.Net.Mail;
using System.Net;

public partial class SignUp : System.Web.UI.Page
{
       string connection =  ConfigurationManager.ConnectionStrings["conn"].ConnectionString;

    protected void Page_Load(object sender, EventArgs e)
    {
        if (Session["username"] != null && Session["email"] != null && Session["passWord"] != null)
        {
            Response.Redirect("login.aspx");
        }
    }
    protected void next_Click(object sender, EventArgs e)
    {
        string recipientEmail = txtemail.Text.Trim();

        Session["usern"] = txtusername.Text;
        Session["emailAddress"] = txtemail.Text;
        Session["passWord"] = txtpassword.Text;

        using (SqlConnection con = new SqlConnection(connection))
        {
            con.Open();
            string query = @"SELECT * FROM LOGIN_DB WHERE EMAIL_ADDRESS = @email";

            using (SqlCommand cmd1 = new SqlCommand(query, con))
            {
                cmd1.Parameters.AddWithValue("@email", recipientEmail);
                SqlDataReader reader = cmd1.ExecuteReader();

                if (reader.HasRows)
                {
                    System.Threading.Thread.Sleep(2000);
                    error.Visible = true;
                    reader.Close();
                    return; 
                }
            }
        }

        // Email does not exist, proceed with sending the verification code
        string verificationCode = new Random().Next(100000, 999999).ToString(); 

        if (SendVerificationEmail(recipientEmail, verificationCode))
        {
            System.Threading.Thread.Sleep(2000);
            card1.Visible = false;
            card2.Visible = true;
            loader1.Visible = false;
            loader2.Visible = true;
            error.Visible = false;
            Session["usern"] = txtusername.Text;
            Session["emailAddress"] = txtemail.Text;
            Session["passWord"] = txtpassword.Text;
            Session["VerificationCode"] = verificationCode;
        }
        else
        {
            ScriptManager.RegisterStartupScript(this, this.GetType(), "alert", "alert('Invalid');", true);
        }
    }
    private bool SendVerificationEmail(string recipientEmail, string verificationCode)
    {
        try
        {
            string senderEmail = "medicalhealthcare07@gmail.com";
            string senderPassword = "crcb lpuw hgct qskn"; 

            MailMessage mail = new MailMessage();
            mail.From = new MailAddress(senderEmail, "Medical Heathcare");
            mail.To.Add(recipientEmail);
            mail.Subject = "Your Verification Code";
            mail.Body = "<p>Your verification code is: <b>" + verificationCode + "</b></p>"; 
            mail.IsBodyHtml = true;

            SmtpClient smtp = new SmtpClient("smtp.gmail.com", 587);
            smtp.Credentials = new NetworkCredential(senderEmail, senderPassword);
            smtp.EnableSsl = true;
            smtp.Send(mail);

            return true;
        }
        catch (Exception)
        {
            return false;
        }
    }

    protected void btnConfirm_Click(object sender, EventArgs e)
    {
        string enteredCode = txtverificationCode.Text.Trim();
        string storedCode = Session["VerificationCode"] as string;

        if (!string.IsNullOrEmpty(storedCode) && enteredCode == storedCode)
        {
            System.Threading.Thread.Sleep(2000);
            Response.Redirect("Personal_Information.aspx");
        }
        else
        {
            System.Threading.Thread.Sleep(2000);
            verificationerror.Visible = true;
        }
    }
}
