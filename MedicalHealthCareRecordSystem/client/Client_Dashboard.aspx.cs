using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Data;
using System.Data.SqlClient;
using System.Configuration;

public partial class _Default : System.Web.UI.Page
{
    string connection = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;
    protected void Page_Load(object sender, EventArgs e)
    {
        if (Session["UserId"] == null)
        {
            Response.Redirect("../login.aspx");
        }
        else
        {
            show();
        }
    }
    private void show()
    {
        if (Session["UserId"] != null)
        {
            string connection = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;
            SqlConnection con = new SqlConnection(connection);
            string query = "SELECT * FROM PATIENT_DB WHERE LOGIN_ID ='" + Session["UserId"] + "'";
            SqlCommand cmd = new SqlCommand(query, con);
            con.Open();
            SqlDataReader dr = cmd.ExecuteReader();
            if (dr.Read())
            {
                string full_name = dr["FULLNAME"].ToString();
                Master.labeluname.Text = full_name;

                dr.Close();
            }
            else
            {
                Response.Redirect("client/Client_Dashboard.aspx");
                dr.Close();
            }

        }
    }
}