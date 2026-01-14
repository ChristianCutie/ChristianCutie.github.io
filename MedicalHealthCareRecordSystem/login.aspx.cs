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
    SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString);
    string email;
    string password;
    string usertype;
    protected void Page_Load(object sender, EventArgs e)
    {
        if (Session["email"] != null)
        {

            Response.Redirect("login.aspx");
        }
        else
        {
            SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString);
            con.Open();
        }
    }
    private void cleartext()
    {
        txtemail.Text = "";
        txtpassword.Text = "";
    }

    protected void btnlogin_Click(object sender, EventArgs e)
    {

        using (SqlConnection constr = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {

            string query = @"SELECT * FROM LOGIN_DB WHERE EMAIL_ADDRESS =@email AND PASSWORD =@pass";

            using (SqlCommand cmd = new SqlCommand(query, constr))
            {
                cmd.Parameters.AddWithValue("@email", txtemail.Text);
                cmd.Parameters.AddWithValue("@pass", txtpassword.Text);
                SqlDataAdapter da = new SqlDataAdapter(cmd);
                DataTable dt = new DataTable();
                constr.Open();
                cmd.ExecuteNonQuery();
                da.Fill(dt);
               
                    if (txtemail.Text == "" && txtpassword.Text == "")
                    {
                        System.Threading.Thread.Sleep(2000);
                        error.Visible = true;
                        lblerror.Text = "Please enter your email and password";
                    }
                    else
                    {
                        if (dt.Rows.Count > 0)
                        {
                            string s_email = txtemail.Text;
                            email = dt.Rows[0]["EMAIL_ADDRESS"].ToString();
                            password = dt.Rows[0]["PASSWORD"].ToString();
                            usertype = dt.Rows[0]["USERTYPE"].ToString();
                        Session["UserName"] = dt.Rows[0]["USERNAME"].ToString();
                        Session["UserId"] = Convert.ToInt32(dt.Rows[0]["id"].ToString());

                            if (email == txtemail.Text && password == txtpassword.Text)
                            {

                                if (usertype == "Patient")
                                {
                                    System.Threading.Thread.Sleep(2000);
                                    Session["UserType"] = usertype;
                                    Session["email"] = s_email;
                                    Session["UserId"].ToString();
                                Session["UserName"].ToString();
                                    Response.Redirect("/client/Client_Dashboard.aspx");
                                //Response.Redirect("Default.aspx");
                            }
                                else if (usertype == "Doctor")
                            {
                                System.Threading.Thread.Sleep(2000);
                                Session["UserType"] = usertype;
                                Session["email"] = s_email;
                                    Session["UserId"].ToString();
                                Session["UserName"].ToString();
                                Response.Redirect("/doctor/Doctor_Dashboard.aspx");
                                //Response.Redirect("Default.aspx");
                            }
                            else if (usertype == "Admin")
                            {
                                System.Threading.Thread.Sleep(2000);
                                    Session["UserType"] = usertype;
                                Session["email"] = s_email;
                                Session["UserId"].ToString();
                                Session["UserName"].ToString();
                                Response.Redirect("/admin/Admin_Dashboard.aspx");
                                //Response.Redirect("Default.aspx");
                            }
                        }
                            else
                        {
                            System.Threading.Thread.Sleep(2000);
                            error.Visible = true;
                                lblerror.Text = "Invalid email and password";
                            }
                        }
                        else
                           {
                            System.Threading.Thread.Sleep(2000);
                            error.Visible = true;
                            lblerror.Text = "Invalid email and password";
                            cleartext();
                        }
                    }

            }
        }

    }
}