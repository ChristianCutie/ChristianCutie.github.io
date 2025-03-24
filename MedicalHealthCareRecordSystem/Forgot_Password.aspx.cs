using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Data;
using System.Data.SqlClient;
using System.Configuration;

public partial class SignUp : System.Web.UI.Page
{
       string connection =  ConfigurationManager.ConnectionStrings["conn"].ConnectionString;

    protected void Page_Load(object sender, EventArgs e)
    {
        if (Session["username"] != null && Session["email"] != null)
        {
            Response.Redirect("login.aspx");
        }
    }

    protected void btnConfirm_Click(object sender, EventArgs e)
    {
        using (SqlConnection con = new SqlConnection(connection))
        {
            con.Open();
            string query = @"SELECT * FROM LOGIN_DB TB1 INNER JOIN PATIENT_DB TB2 ON TB1.id = TB2.LOGIN_ID WHERE TB1.EMAIL_ADDRESS = @email AND TB2.CONTACT = @contact";


            using (SqlCommand cmd1 = new SqlCommand(query, con))
            {
                cmd1.Parameters.AddWithValue("@email", txtemail.Text);
                cmd1.Parameters.AddWithValue("@contact", txtphonenumber.Text);

                SqlDataReader reader = cmd1.ExecuteReader();


                if (reader.HasRows)
                {
                    //ScriptManager.RegisterStartupScript(this, GetType(), "Alert", "alert('This email address has already use. if you have already account you can use forgot password page to reactivate your account');", true);

                    System.Threading.Thread.Sleep(2000);
                    card1.Visible = false;
                    card2.Visible = true;
                }
                else
                {
                    // If no rows are returned, the time is available for the selected doctor
                    System.Threading.Thread.Sleep(2000);
                    reader.Close();
                    error.Visible = true;
                }
                con.Close();
            }
        }
    }

    protected void btnsave_Click(object sender, EventArgs e)
    {
        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {
            con.Open();

            string query1 = @"SELECT * FROM LOGIN_DB TB1 INNER JOIN PATIENT_DB TB2 ON TB1.id = TB2.LOGIN_ID WHERE TB1.EMAIL_ADDRESS = @email AND TB2.CONTACT = @contact";


            using (SqlCommand cmd1 = new SqlCommand(query1, con))
            {
                cmd1.Parameters.AddWithValue("@email", txtemail.Text);
                cmd1.Parameters.AddWithValue("@contact", txtphonenumber.Text);

                SqlDataReader reader = cmd1.ExecuteReader();

                if (reader.HasRows)
                {
                    reader.Close();
                    System.Threading.Thread.Sleep(2000);
                    string query = @"UPDATE LOGIN_DB SET PASSWORD=@pass WHERE EMAIL_ADDRESS = @email";
                    using (SqlCommand cmd = new SqlCommand(query, con))
                    {
                        cmd.Parameters.AddWithValue("@pass", txtnewpassword.Text);
                        cmd.Parameters.AddWithValue("@email", txtemail.Text);
                        cmd.ExecuteNonQuery();
                        Response.Write("<script>alert('Successfully Changed! Please login your account.'); window.location='login.aspx';</script>");
                    }
                }
            }

        }
    }
}
