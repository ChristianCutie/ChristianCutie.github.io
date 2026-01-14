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
        if (!IsPostBack)
        {
            GetDataHistory();
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
            case "canceled":
                return "badge bg-danger rounded-pill";
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
                Response.Redirect("client/History.aspx");
                dr.Close();
            }

        }
    }
    private void GetDataHistory()
    {
        SqlConnection con = new SqlConnection(connection);
        SqlCommand cmd = new SqlCommand(@"
        SELECT h.id, h.PATIENT_ID, h.PATIENT_NAME, h.DOCTOR, h.SPECIALTY, h.STATUS, h.DATE_ADDED, h.TIME, h.DATE 
        FROM HISTORY_TB h
        INNER JOIN (
            SELECT PATIENT_ID, DOCTOR, SPECIALTY, MAX(DATE_ADDED) AS LatestDate
            FROM HISTORY_TB
            WHERE PATIENT_ID = @UserId
            GROUP BY PATIENT_ID, DOCTOR, SPECIALTY
        ) latest ON h.PATIENT_ID = latest.PATIENT_ID 
               AND h.DOCTOR = latest.DOCTOR 
               AND h.SPECIALTY = latest.SPECIALTY
               AND h.DATE_ADDED = latest.LatestDate
        WHERE h.PATIENT_ID = @UserId", con);

        cmd.Parameters.AddWithValue("@UserId", Session["UserId"]);
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

    protected void linkView_Click(object sender, EventArgs e)
    {
        LinkButton lnkBtn = sender as LinkButton;
        GridViewRow gdrow = lnkBtn.NamingContainer as GridViewRow;
        string patientId = myappointmentlist.DataKeys[gdrow.RowIndex].Value.ToString();

        string doctorName = gdrow.Cells[1].Text; 
        string specialty = gdrow.Cells[2].Text; 

        using (SqlConnection con = new SqlConnection(connection))
        {
            con.Open();
            string query = @"SELECT * FROM HISTORY_TB WHERE PATIENT_ID = @patient_id AND DOCTOR = @doctor_name AND SPECIALTY = @specialty ORDER BY id DESC";

            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                cmd.Parameters.AddWithValue("@patient_id", Session["UserId"]);
                cmd.Parameters.AddWithValue("@doctor_name", doctorName);
                cmd.Parameters.AddWithValue("@specialty", specialty);

                SqlDataAdapter da = new SqlDataAdapter(cmd);
                DataTable dt = new DataTable();
                da.Fill(dt);

                if (dt.Rows.Count > 0)
                {
                    rptOverAllHistory.DataSource = dt;
                    rptOverAllHistory.DataBind();
                }
                else
                {
                    rptOverAllHistory.DataSource = null;
                    rptOverAllHistory.DataBind();
                }
            }
        }

        GetDataHistory();
    }
}