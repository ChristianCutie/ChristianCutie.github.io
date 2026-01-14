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
    private History history = new History();
    protected void Page_Load(object sender, EventArgs e)
    {
        if (Session["UserId"] == null)
        {
            Response.Redirect("../login.aspx");
        }
        GetData();
    }
    protected string GetStatusCssClass(string status)
    {
        switch (status.ToLower())
        {
            case "completed":
                return "badge bg-success rounded-pill";
            case "pending":
                return "badge bg-secondary rounded-pill";
            case "approved":
                return "badge bg-warning rounded-pill";
            default:
                return "badge bg-secondary rounded-pill";
        }
    }
    private void GetData()
    {

        SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString);

        SqlCommand cmd = new SqlCommand(@"SELECT * FROM APPOINTMENT_DB WHERE Status ='Pending' ORDER BY id DESC", con);
        SqlDataAdapter da = new SqlDataAdapter(cmd);
        DataSet ds = new DataSet();
        da.Fill(ds);

        if (ds.Tables[0].Rows.Count > 0)
        {
            doctorlistappt.DataSource = ds;
            doctorlistappt.DataBind();
            doctorlistappt.UseAccessibleHeader = true;
            doctorlistappt.HeaderRow.TableSection = TableRowSection.TableHeader;
        }
        else
        {
            ds.Tables[0].Rows.Add(ds.Tables[0].NewRow());
            doctorlistappt.DataSource = ds;
            doctorlistappt.DataBind();
            int columncount = doctorlistappt.Rows[0].Cells.Count;
            doctorlistappt.Rows[0].Cells.Clear();
            doctorlistappt.Rows[0].Cells.Add(new TableCell());
            doctorlistappt.Rows[0].Cells[0].ColumnSpan = columncount;
            doctorlistappt.Rows[0].Cells[0].Text = "No matching records found";
            doctorlistappt.Rows[0].Cells[0].CssClass = "text-center";
            doctorlistappt.UseAccessibleHeader = true;
            doctorlistappt.HeaderRow.TableSection = TableRowSection.TableHeader;
        }
    }

    protected void btnapprove_Click(object sender, EventArgs e)
    {
        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {
            con.Open();
            string query = @"UPDATE APPOINTMENT_DB SET STATUS=@status WHERE id = @id";
            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                cmd.Parameters.AddWithValue("@id", lblidvisible.Text);
                cmd.Parameters.AddWithValue("@status", "Approved");
                cmd.ExecuteNonQuery();
                modal1.Hide();
                HistoryApprovedStatus();
                ScriptManager.RegisterStartupScript(this, GetType(), "Alert", "alert('Appointment Approved!');", true);
            }
            GetData();
        }
    }

    protected void btndecline_Click(object sender, EventArgs e)
    {
        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {
            con.Open();
            string query = @"UPDATE APPOINTMENT_DB SET STATUS=@status WHERE id = @id";
            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                cmd.Parameters.AddWithValue("@id", lblidvisible.Text);
                cmd.Parameters.AddWithValue("@status", "Canceled");
                cmd.ExecuteNonQuery();
            }
        }

        modal1.Hide();
        HistoryCanceledStatus();
        GetData();
        ScriptManager.RegisterStartupScript(this, GetType(), "alertMessage",
            "alert('Appointment canceled.');", true);
    }

    protected void btnmodal_Click(object sender, EventArgs e)
    {
        LinkButton lnkbtndel = sender as LinkButton;
        GridViewRow gdrow = lnkbtndel.NamingContainer as GridViewRow;
        int fileid = Convert.ToInt32(doctorlistappt.DataKeys[gdrow.RowIndex].Value.ToString());

        using (SqlConnection cons = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {
            cons.Open();
            using (SqlCommand cmd = new SqlCommand(@"SELECT * FROM APPOINTMENT_DB WHERE id=@id", cons))
            {
                cmd.Parameters.AddWithValue("@id", fileid);
                using (SqlDataAdapter da = new SqlDataAdapter(cmd))
                {
                    DataTable dt = new DataTable();
                    da.Fill(dt);
                    cons.Close();
                    foreach (DataRow dr in dt.Rows)
                    {
                        this.lblidvisible.Text = dr["id"].ToString();
                        this.lblPatientId.Text = dr["PATIENT_ID"].ToString();
                        this.lblDoctorId.Text = dr["DOCTOR_ID"].ToString();
                        this.lbldocName.Text = dr["DOCTOR"].ToString();
                        this.lblpname.Text = dr["PATIENT_NAME"].ToString();
                        this.lbldate.Text = dr["DATE"].ToString();
                        this.lbltime.Text = dr["TIME"].ToString();
                        this.lbltype.Text = dr["TYPE"].ToString();
                        this.lblstatus.Text = dr["STATUS"].ToString();
                        this.txtmessage.Text = dr["MESSAGE"].ToString();
                        modal1.Show();
                        
                    }
                    GetData();
                }
            }
        }
    }
    private void HistoryCanceledStatus()
    {
        History list = new History()
        {
            PATIENT_ID = Convert.ToInt32(lblPatientId.Text),
            DOCTOR_ID = Convert.ToInt32(lblDoctorId.Text),
            DOCTOR = lbldocName.Text,
            PATIENT_NAME = lblpname.Text,
            SPECIALTY = lbltype.Text,
            TIME = lbltime.Text,
            DATE = lbldate.Text,
            STATUS = "Canceled",
            DATE_ADDED = DateTime.Now
        };
        history.InsertHistory(list);
    }
    private void HistoryApprovedStatus()
    {
        History list = new History()
        {
            PATIENT_ID = Convert.ToInt32(lblPatientId.Text),
            DOCTOR_ID = Convert.ToInt32(lblDoctorId.Text),
            DOCTOR = lbldocName.Text,
            PATIENT_NAME = lblpname.Text,
            SPECIALTY = lbltype.Text,
            TIME = lbltime.Text,
            DATE = lbldate.Text,
            STATUS = "Approved",
            DATE_ADDED = DateTime.Now
        };
        history.InsertHistory(list);
    }
    protected void btnclose_Click(object sender, EventArgs e)
    {
        modal1.Hide();
        GetData();
        
    }
}