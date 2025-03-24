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
            GetData();
        }
        if (!IsPostBack)
        {
            GetData();
        }
    }
    protected string GetStatusCssClass(string status)
    {
        switch (status.ToLower())
        {
            case "completed":
                return "badge bg-success rounded-pill";
            case "approved":
                return "badge bg-warning rounded-pill";
            case "follow up":
                return "badge bg-primary rounded-pill";
            default:
                return "badge bg-secondary rounded-pill";
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
                Response.Redirect("client/My_Appointment.aspx");
                dr.Close();
            }

        }
    }
    private void GetData()
    {

        SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString);

        SqlCommand cmd = new SqlCommand(@"SELECT * FROM APPOINTMENT_DB WHERE PATIENT_ID = '" + Session["UserId"] + "' AND (STATUS = 'Pending' OR STATUS = 'Approved' OR STATUS = 'Follow up')", con);
        SqlDataAdapter da = new SqlDataAdapter(cmd);
        DataSet ds = new DataSet();
        da.Fill(ds);

        if (ds.Tables[0].Rows.Count > 0)
        {
            myappointmentlist.DataSource = ds;
            myappointmentlist.DataBind();
            myappointmentlist.UseAccessibleHeader = true;
            myappointmentlist.HeaderRow.TableSection = TableRowSection.TableHeader;
        }
        else
        {
            ds.Tables[0].Rows.Add(ds.Tables[0].NewRow());
            myappointmentlist.DataSource = ds;
            myappointmentlist.DataBind();
            int columncount = myappointmentlist.Rows[0].Cells.Count;
            myappointmentlist.Rows[0].Cells.Clear();
            myappointmentlist.Rows[0].Cells.Add(new TableCell());
            myappointmentlist.Rows[0].Cells[0].ColumnSpan = columncount;
            myappointmentlist.Rows[0].Cells[0].Text = "No matching records found";
            myappointmentlist.Rows[0].Cells[0].CssClass = "text-center";
            myappointmentlist.UseAccessibleHeader = true;
            myappointmentlist.HeaderRow.TableSection = TableRowSection.TableHeader;
        }
    }

    protected void btnnew_Click(object sender, EventArgs e)
    {
        Response.Redirect("Add_new.aspx");
    }

    protected void linkdelete_Click(object sender, EventArgs e)
    {
        GetData();
        LinkButton lnkbtndel = sender as LinkButton;
        GridViewRow gdrow = lnkbtndel.NamingContainer as GridViewRow;
        string fileid = myappointmentlist.DataKeys[gdrow.RowIndex].Value.ToString();
        string query = "UPDATE APPOINTMENT_DB SET STATUS = @status WHERE id = @ID";
        using (SqlConnection conn = new SqlConnection(connection))
        {
            using (SqlCommand cmd = new SqlCommand(query, conn))
            {
                cmd.Parameters.AddWithValue("@ID", fileid);
                cmd.Parameters.AddWithValue("@status", "Removed");
                conn.Open();
                cmd.ExecuteNonQuery();
                conn.Close();
                ScriptManager.RegisterStartupScript(this, GetType(), "Alert", "alert('Appointment deleted!');", true);
            }
        }
    }
}