using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Data;
using System.Data.SqlClient;
using System.Configuration;
using System.Text;
using System.Text.RegularExpressions;
using System.Net.Mail;
using System.Net;

public partial class _Default : System.Web.UI.Page
{
   string connection = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;
    private History history_List = new History();
    DateTime dateTime = DateTime.Now;
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
            string query = "SELECT * FROM DOCTOR_DB WHERE F_DOC_ID ='" + Session["UserId"] + "'";
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
                Response.Redirect("/doctor/MyAppointment.aspx");
                dr.Close();
            }
        }
    }
    private void GetData()
    {
        SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString);

        SqlCommand cmd = new SqlCommand(@"SELECT * FROM APPOINTMENT_DB WHERE DOCTOR_ID = '" + Session["UserId"] + "' AND (STATUS = 'Approved' OR STATUS = 'Follow up')", con);
        SqlDataAdapter da = new SqlDataAdapter(cmd);
        DataSet ds = new DataSet();
        da.Fill(ds);

        if (ds.Tables[0].Rows.Count > 0)
        {
            grvpending.DataSource = ds;
            grvpending.DataBind();
            grvpending.UseAccessibleHeader = true;
            grvpending.HeaderRow.TableSection = TableRowSection.TableHeader;
        }
        else
        {
            ds.Tables[0].Rows.Add(ds.Tables[0].NewRow());
            grvpending.DataSource = ds;
            grvpending.DataBind();
            int columncount = grvpending.Rows[0].Cells.Count;
            grvpending.Rows[0].Cells.Clear();
            grvpending.Rows[0].Cells.Add(new TableCell());
            grvpending.Rows[0].Cells[0].ColumnSpan = columncount;
            grvpending.Rows[0].Cells[0].Text = "No matching records found";
            grvpending.Rows[0].Cells[0].CssClass = "text-center";
            grvpending.UseAccessibleHeader = true;
            grvpending.HeaderRow.TableSection = TableRowSection.TableHeader;
        }
    }

    protected void btnmodal_Click(object sender, EventArgs e)
    {
        LinkButton lnkbtndel = sender as LinkButton;
        GridViewRow gdrow = lnkbtndel.NamingContainer as GridViewRow;
        int fileid = Convert.ToInt32(grvpending.DataKeys[gdrow.RowIndex].Value.ToString());

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
                        this.lblvpatientId.Text = dr["PATIENT_ID"].ToString();
                        this.lblvdoctorId.Text = dr["DOCTOR_ID"].ToString();
                        this.lblvpatientName.Text = dr["PATIENT_NAME"].ToString();
                        this.lblvdoctorName.Text = dr["DOCTOR"].ToString();
                        this.lblvtime.Text = dr["TIME"].ToString();
                        this.lblvtype.Text = dr["TYPE"].ToString();
                        this.lbldates.Text = dr["DATE"].ToString();
                        this.txtfollowdate.Text = dr["DATE"].ToString();
                        this.txtremarks.Text = dr["MESSAGE"].ToString();

                        modal1.Show();
                        GetData();
                    }
                }
            }
        }
        modal1.Show();
        GetData();
    }

    protected string StripHtml(string input)
    { 
        if (string.IsNullOrWhiteSpace(input))
            return string.Empty;

        return Regex.Replace(input, "<.*?>", string.Empty);
    }
    private void SaveHistoryFollowUp()
    {
        History h_list = new History()
        {
            PATIENT_ID = int.Parse(lblvpatientId.Text),
            DOCTOR_ID = int.Parse(lblvdoctorId.Text),
            DOCTOR = lblvdoctorName.Text,
            PATIENT_NAME = lblvpatientName.Text,
            SPECIALTY = lblvtype.Text,
            TIME = lblvtime.Text,
            DATE = lbldates.Text,
            STATUS = "Follow Up",
            DATE_ADDED = dateTime

        };
        history_List.InsertHistory(h_list);
    }
    private void SaveHistoryCompleted(int patient_id, int doctor_id, string patient_Name, string doctor_Name, string specialty, string time, string date)
    {
        History h_list = new History()
        {
            PATIENT_ID = patient_id,
            DOCTOR_ID = doctor_id,
            DOCTOR = doctor_Name,
            PATIENT_NAME = patient_Name,
            SPECIALTY = specialty,
            TIME = time,
            DATE = date,
            STATUS = "Completed",
            DATE_ADDED = dateTime
        };
        history_List.InsertHistory(h_list);
    }
    protected void btnapprove_Click(object sender, EventArgs e)
    {
        string status = "Follow up";
        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {
            con.Open();
            string query = @"SELECT * FROM LOGIN_DB l1 INNER JOIN APPOINTMENT_DB a1 ON l1.id = a1.PATIENT_ID WHERE PATIENT_ID = '" + lblvpatientId.Text + "'";

            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                SqlDataAdapter da = new SqlDataAdapter(cmd);
                DataTable dt = new DataTable();
                da.Fill(dt);

                if (dt.Rows.Count > 0)
                {
                    string recipientEmail = dt.Rows[0]["EMAIL_ADDRESS"].ToString();

                    string query_2 = @"UPDATE APPOINTMENT_DB SET MESSAGE=@msg, STATUS = @status, DATE =@date WHERE id = @id";
                    using (SqlCommand cmd2 = new SqlCommand(query_2, con))
                    {
                        cmd2.Parameters.AddWithValue("@id", lblidvisible.Text);
                        cmd2.Parameters.AddWithValue("@msg", txtremarks.Text);
                        cmd2.Parameters.AddWithValue("@date", txtfollowdate.Text);
                        cmd2.Parameters.AddWithValue("@status", status);
                        cmd2.ExecuteNonQuery();
                        modal1.Hide();
                        Response.Write("<script>alert('Remarks and follow up schedule has been updated');</script>");
                        SendVerificationEmail(recipientEmail, status);
                        GetData();
                        chkfollowup.Checked = false;
                    }
                    SaveHistoryFollowUp();
                }
            }


            
        }
    }
    protected string Truncate(string input, int length)
    {
        if (string.IsNullOrEmpty(input))
        {
            return string.Empty;
        }

        if (input.Length > length)
        {
            return input.Substring(0, length) + "...";
        }

        return input;
    }


    protected void btnclose_Click(object sender, EventArgs e)
    {
        chkfollowup.Checked = false;
        GetData();
    }

    protected void btncompleted_Click(object sender, EventArgs e)
    {
        LinkButton linkcomplete = sender as LinkButton;
        GridViewRow gdrow = linkcomplete.NamingContainer as GridViewRow;
        int fileid = Convert.ToInt32(grvpending.DataKeys[gdrow.RowIndex].Value.ToString());
        string status = "Completed";

        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {
            con.Open();
            string query = @"UPDATE APPOINTMENT_DB SET STATUS = @status WHERE id = @id";
            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                cmd.Parameters.AddWithValue("@id", fileid);
                cmd.Parameters.AddWithValue("@status", status);
                cmd.ExecuteNonQuery();
            }

            string query_2 = @"SELECT * FROM APPOINTMENT_DB WHERE id=@id";

            using (SqlCommand cmd = new SqlCommand(query_2, con))
            {
                cmd.Parameters.AddWithValue("@id", fileid);
                SqlDataAdapter da = new SqlDataAdapter(cmd);
                DataTable dt = new DataTable();
                da.Fill(dt);

                if (dt.Rows.Count > 0)
                {
                    int patient_id = Convert.ToInt32(dt.Rows[0]["PATIENT_ID"].ToString());
                    int doctor_id = Convert.ToInt32(dt.Rows[0]["DOCTOR_ID"].ToString());
                    string patient_Name = dt.Rows[0]["PATIENT_NAME"].ToString();
                    string doctor_Name = dt.Rows[0]["DOCTOR"].ToString();
                    string specialty = dt.Rows[0]["TYPE"].ToString();
                    string time = dt.Rows[0]["TIME"].ToString();
                    string date = dt.Rows[0]["DATE"].ToString();

                    GetEmail(patient_id, status);
                    SaveHistoryCompleted(patient_id, doctor_id, patient_Name, doctor_Name, specialty, time, date);
                    GetData();
                    ScriptManager.RegisterStartupScript(this, GetType(), "alertMessage","alert('Appointment completed and moved to the history page.');", true);
                }

            }
        }
    }
    private void GetEmail(int patient_id, string status)
    {
        using (SqlConnection con = new SqlConnection(connection))
        {
            con.Open();

            string query = @"SELECT * FROM LOGIN_DB WHERE id = @id";

            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                cmd.Parameters.AddWithValue("@id", patient_id);
                SqlDataAdapter da = new SqlDataAdapter(cmd);
                DataTable dt = new DataTable();
                da.Fill(dt);

                if (dt.Rows.Count > 0)
                {
                    string recipientEmail = dt.Rows[0]["EMAIL_ADDRESS"].ToString();

                    SendVerificationEmail(recipientEmail, status);
                }
            }
        }
    }
    private bool SendVerificationEmail(string recipientEmail, string status)
    {
        try
        {
            string senderEmail = "medicalhealthcare07@gmail.com";
            string senderPassword = "crcb lpuw hgct qskn";

            MailMessage mail = new MailMessage();
            mail.From = new MailAddress(senderEmail, "Medical Heathcare");
            mail.To.Add(recipientEmail);
            mail.Subject = "Appointment Status Update";
            mail.Body = "<p>The status of your appointment has been updated to <b>" + status.ToUpper() + "</b>. To check your appointment details, please log in to your account here: <a href=\"https://medicahc-001-site1.jtempurl.com/\">https://medicahc-001-site1.jtempurl.com/</a></p>";
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
}
