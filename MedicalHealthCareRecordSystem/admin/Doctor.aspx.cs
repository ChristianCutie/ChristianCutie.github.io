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

public partial class _Default : System.Web.UI.Page
{
    SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString);
    private UsersChat userList = new UsersChat();
    private History history = new History();
    protected void Page_Load(object sender, EventArgs e)
    {
        if (Session["UserId"] == null)
        {
            Response.Redirect("../login.aspx");
        }
        if (!IsPostBack)
        {
            BindAllTimeIntervalsToDropdowns();
            GetData();
        }
    }
    public class TimeItem
    {
        public TimeSpan TimeValue { get; set; }
        public string DisplayText { get; set; }
        public int TotalMinutes { get; set; }
    }
    protected string GetStatusCssClass(string status)
    {
        switch (status.ToLower())
        {
            case "active":
                return "badge bg-success rounded-pill";
            case "deactivated":
                return "badge bg-warning rounded-pill";
            default:
                return "badge bg-secondary rounded-pill";
        }
    }
    private void SaveTime( int database1Id)
    {
        try
        {
            // Get selected time range
            int startMinutes = Convert.ToInt32(ddltimein.SelectedValue);
            int endMinutes = Convert.ToInt32(ddltimeout.SelectedValue);

            // Get DateID from LOGIN_DB TABLE
            int dateId = database1Id; 

            // Validate time range
            if (endMinutes <= startMinutes)
            {
                lblerror2.Text = "Time Out must be after Time In";
                return;
            }

            // Calculate number of 30-minute intervals
            int intervalCount = (endMinutes - startMinutes) / 30;

            // Create a list to display the generated time slots
            List<string> displayTimeSlots = new List<string>();

            // Database connection
            using (SqlConnection conn = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
            {
                conn.Open();

                // Clear previous entries for this DateID
                SqlCommand clearCmd = new SqlCommand("DELETE FROM TimeSlots WHERE DateID = @DateID", conn);
                clearCmd.Parameters.AddWithValue("@DateID", dateId);
                clearCmd.ExecuteNonQuery();

                // Insert new intervals
                for (int i = 0; i <= intervalCount; i++)
                {
                    int currentMinutes = startMinutes + (i * 30);
                    TimeSpan currentTime = TimeSpan.FromMinutes(currentMinutes);

                    // Format for display
                    string amPm = currentTime.Hours < 12 ? "AM" : "PM";
                    int displayHour = currentTime.Hours % 12;
                    if (displayHour == 0) displayHour = 12;
                    string displayText = string.Format("{0}:{1:00} {2}", displayHour, currentTime.Minutes, amPm);

                    displayTimeSlots.Add(displayText);

                    // Save to database
                    SqlCommand cmd = new SqlCommand(
                        "INSERT INTO TimeSlots (DateID, TimeSlot) VALUES (@DateID, @TimeSlot)", conn);
                    cmd.Parameters.AddWithValue("@DateID", dateId);
                    cmd.Parameters.AddWithValue("@TimeSlot", currentTime);
                    cmd.ExecuteNonQuery();
                }
            }

        }
        catch (Exception ex)
        {
            lblerror2.Text = ex.Message;
        }
    }
    private void BindAllTimeIntervalsToDropdowns()
    {
        // Create a list to hold the time intervals
        List<TimeItem> timeIntervals = new List<TimeItem>();

        // Generate times at 30-minute intervals for a full day (48 intervals)
        for (int i = 0; i < 48; i++)
        {
            // Calculate hours and minutes
            int hours = i / 2;
            int minutes = (i % 2) * 30;
            TimeSpan timeSpan = new TimeSpan(hours, minutes, 0);

            // Create formatted display text
            string amPm = hours < 12 ? "AM" : "PM";
            int displayHour = hours % 12;
            if (displayHour == 0) displayHour = 12;
            string displayText = string.Format("{0}:{1:00} {2}", displayHour, minutes, amPm);

            timeIntervals.Add(new TimeItem
            {
                TimeValue = timeSpan,
                DisplayText = displayText,
                TotalMinutes = (int)timeSpan.TotalMinutes
            });
        }

        // Bind to dropdown lists
        ddltimein.DataSource = timeIntervals;
        ddltimein.DataTextField = "DisplayText";
        ddltimein.DataValueField = "TotalMinutes";
        ddltimein.DataBind();

        ddltimeout.DataSource = timeIntervals;
        ddltimeout.DataTextField = "DisplayText";
        ddltimeout.DataValueField = "TotalMinutes";
        ddltimeout.DataBind();

        // Set default values (8:00 AM to 5:00 PM)
        ddltimein.Items.FindByText("8:00 AM").Selected = true;
        ddltimeout.Items.FindByText("5:00 PM").Selected = true;
    }
    protected void btnadd_Click(object sender, EventArgs e)
    {
        txtfullname.Text = "";
        txtemail.Text = "";
        txtpassword.Text = "";
        txtconfirmpassword.Text = "";
        chkMonday.Checked = false;
        chkTuesday.Checked = false;
        chkWednesday.Checked = false;
        chkThursday.Checked = false;
        chkFriday.Checked = false;
        chkSaturday.Checked = false;
        chkSunday.Checked = false;
        //ddltimein.SelectedValue = "";
        //ddltimeout.SelectedValue = "";
        success.Visible = false;
        GetData();
        modal1.Show();
        //ddltimein.ClearSelection();
        //ddltimeout.ClearSelection();
    }
    private void GetData()
    {

        SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString);

        SqlCommand cmd = new SqlCommand(@"SELECT * FROM DOCTOR_DB", con);
        SqlDataAdapter da = new SqlDataAdapter(cmd);
        DataSet ds = new DataSet();
        da.Fill(ds);

        if (ds.Tables[0].Rows.Count > 0)
        {
            doctorlist.DataSource = ds;
            doctorlist.DataBind();
            doctorlist.UseAccessibleHeader = true;
            doctorlist.HeaderRow.TableSection = TableRowSection.TableHeader;
        }
        else
        {
            ds.Tables[0].Rows.Add(ds.Tables[0].NewRow());
            doctorlist.DataSource = ds;
            doctorlist.DataBind();
            int columncount = doctorlist.Rows[0].Cells.Count;
            doctorlist.Rows[0].Cells.Clear();
            doctorlist.Rows[0].Cells.Add(new TableCell());
            doctorlist.Rows[0].Cells[0].ColumnSpan = columncount;
            doctorlist.Rows[0].Cells[0].Text = "No matching records found";
            doctorlist.Rows[0].Cells[0].CssClass = "text-center";
            doctorlist.UseAccessibleHeader = true;
            doctorlist.HeaderRow.TableSection = TableRowSection.TableHeader;
        }
    }

    protected void btnsubmit_Click(object sender, EventArgs e)
    {
        string connectionString = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;

        int database1Id;
        if (ddltimein.SelectedValue == "0" || ddltimeout.SelectedValue == "0")
        {
            Response.Write("<script>alert('Please fill the required field');</script>");
            modal1.Show();
        }
        else
        {
            using (SqlConnection con_login = new SqlConnection(connectionString))
            {

                string query_login = @"INSERT INTO LOGIN_DB (USERNAME, EMAIL_ADDRESS, PASSWORD, USERTYPE) VALUES 
                                (@fname, @email, @password, @utype); SELECT SCOPE_IDENTITY();";

                using (SqlCommand cmd_login = new SqlCommand(query_login, con_login))
                {
                    cmd_login.Parameters.AddWithValue("@fname", txtfullname.Text);
                    cmd_login.Parameters.AddWithValue("@email", txtemail.Text);
                    cmd_login.Parameters.AddWithValue("@password", txtpassword.Text);
                    cmd_login.Parameters.AddWithValue("@utype", "Doctor");
                    con_login.Open();
                    database1Id = Convert.ToInt32(cmd_login.ExecuteScalar());
                }

            }

            // Save UserChat at the same time
            UsersChat UserList = new UsersChat()
            {
                UserID = database1Id.ToString(),
                UserName = txtfullname.Text,
                UserType = "Doctor",
                Email = txtemail.Text,
                Password = txtpassword.Text,
                DateRegistered = DateTime.Now
            };
            userList.InsertNewChatUser(UserList);

            // Save doctor information at the same time
            using (SqlConnection con_doc = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
            {
                con_doc.Open();
                string query_doc = @"INSERT INTO DOCTOR_DB (F_DOC_ID, FULLNAME, DEGREE, AVAILABLE_TIME, EMAIL_ADDRESS, PASSWORD, STATUS) VALUES 
                    (@id, @fn, @deg, @avtime, @email, @password, @status)";

                string available_time = ddltimein.SelectedItem.Text + " - " + ddltimeout.SelectedItem.Text;

                using (SqlCommand cmd_doc = new SqlCommand(query_doc, con_doc))
                {
                    cmd_doc.Parameters.AddWithValue("@fn", txtfullname.Text);
                    cmd_doc.Parameters.AddWithValue("@deg", ddlspecialty.SelectedItem.Text); //SPECIALTY - New column name
                    cmd_doc.Parameters.AddWithValue("@avtime", available_time);
                    cmd_doc.Parameters.AddWithValue("@email", txtemail.Text);
                    cmd_doc.Parameters.AddWithValue("@password", txtpassword.Text);
                    cmd_doc.Parameters.AddWithValue("@status", "Active");
                    cmd_doc.Parameters.AddWithValue("@id", database1Id);
                    cmd_doc.ExecuteNonQuery();
                    con_doc.Close();
                }
            }

            // Then, use that ID as a foreign key in Database2
            List<string> selected = new List<string>();

            if (chkMonday.Checked) selected.Add("Monday");
            if (chkTuesday.Checked) selected.Add("Tuesday");
            if (chkWednesday.Checked) selected.Add("Wednesday");
            if (chkThursday.Checked) selected.Add("Thursday");
            if (chkFriday.Checked) selected.Add("Friday");
            if (chkSaturday.Checked) selected.Add("Saturday");
            if (chkSunday.Checked) selected.Add("Sunday");

            // Insert each selected day into the database as a separate row
            using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
            {
                string query = @"INSERT INTO AVAILABLE_TIME_DB (AVAILABLE_ID, DAY) VALUES (@avId, @day)";

                foreach (string day in selected)
                {
                    using (SqlCommand cmd = new SqlCommand(query, con))
                    {
                        cmd.Parameters.AddWithValue("@avId", database1Id); 
                        cmd.Parameters.AddWithValue("@day", day);
                        con.Open();
                        cmd.ExecuteNonQuery();
                        con.Close();
                        SaveTime(database1Id);
                        GetData();
                        success.Visible = true;
                        lblsuccess.Text = "Successfully created!";
                        modal1.Hide();
                        ddltimein.ClearSelection();
                        ddltimeout.ClearSelection();
                    }
                }
            }
        }
    }

    protected void cvCheckboxGroup_ServerValidate(object source, ServerValidateEventArgs args)
    {
        int checkedCount = 0;

        if (chkMonday.Checked) checkedCount++;
        if (chkTuesday.Checked) checkedCount++;
        if (chkWednesday.Checked) checkedCount++;
        if (chkThursday.Checked) checkedCount++;
        if (chkFriday.Checked) checkedCount++;
        if (chkSaturday.Checked) checkedCount++;
        if (chkSunday.Checked) checkedCount++;

        // Check if at least 5 are checked
        args.IsValid = (checkedCount >= 5);
    }

    protected void doctorlist_RowDataBound(object sender, GridViewRowEventArgs e)
    {
        if (e.Row.RowType == DataControlRowType.DataRow)
        {
            // Find the label for displaying the password
            Label lblPassword = (Label)e.Row.FindControl("lblPassword");

            if (lblPassword != null)
            {
                // Get the original password value
                object passwordObj = DataBinder.Eval(e.Row.DataItem, "PASSWORD");
                string password = passwordObj != null ? passwordObj.ToString() : string.Empty;

                // Mask the password with asterisks
                if (!string.IsNullOrEmpty(password))
                {
                    lblPassword.Text = new string('•', password.Length);
                }
            }
        }
    }

    protected void linkdelete_Click(object sender, EventArgs e)
    {
        LinkButton lnkbtndel = sender as LinkButton;
        GridViewRow gdrow = lnkbtndel.NamingContainer as GridViewRow;
        string fileid = doctorlist.DataKeys[gdrow.RowIndex].Value.ToString();
        string query = "UPDATE DOCTOR_DB SET STATUS = @status WHERE id = @ID";
        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {
            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                cmd.Parameters.AddWithValue("@ID", fileid);
                cmd.Parameters.AddWithValue("@status", "Removed");
                con.Open();
                cmd.ExecuteNonQuery();
                con.Close();
                GetData();
                ScriptManager.RegisterStartupScript(this, GetType(), "Alert", "alert('Account successfully deleted!');", true);
            }
        }
    }

    protected void btnclose_Click(object sender, EventArgs e)
    {
        GetData();
    }
}