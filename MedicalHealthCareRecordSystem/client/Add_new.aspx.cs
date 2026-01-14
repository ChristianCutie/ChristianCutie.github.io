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
    private DataTable eventTable;
    private History history = new History();
    protected void Page_Load(object sender, EventArgs e)
    {
        if (!IsPostBack)
        {
            ListViewTab();
            DayShow();
            LoadDoctor();
            txtdate.Attributes["min"] = DateTime.Now.ToString("yyyy-MM-dd");
        }
        if (Session["UserId"] == null)
        {
            Response.Redirect("../login.aspx");
        }
        else
        {
            show();
        }
        LoadEvents();
    }
    private void show()
    {
        if (Session["UserId"] != null)
        {
            string connection = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;
            SqlConnection con = new SqlConnection(connection);
            string query = "SELECT * FROM PATIENT_DB WHERE LOGIN_ID =@UserId";
            SqlCommand cmd = new SqlCommand(query, con);
            cmd.Parameters.AddWithValue("@UserId", Session["UserId"]);
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
                Response.Redirect("Add_new.aspx");
                dr.Close();
            }

        }
    }
    private void History()
    {
        History list = new History()
        {
            PATIENT_ID = Convert.ToInt32(Session["UserId"]),
            DOCTOR_ID = Convert.ToInt32(ddldoctor.SelectedValue),
            DOCTOR = txtdocname.Text,
            PATIENT_NAME = Master.labeluname.Text,
            SPECIALTY = ddltype.SelectedItem.Text,
            TIME = ddltime.SelectedItem.Text,
            DATE = txtdate.Text,
            STATUS = "Pending",
            DATE_ADDED = DateTime.Now
        };
        history.InsertHistory(list);
    }
    private void LoadDoctor()
    {
        using (SqlConnection con = new SqlConnection(connection))
        {
            con.Open();

            ddldoctor.Items.Clear();
            ddldoctor.Items.Add(new ListItem("--Which doctor do you go to?--", ""));
            ddldoctor.AppendDataBoundItems = true;
            SqlCommand cmd = new SqlCommand(@"SELECT * FROM DOCTOR_DB", con);
            SqlDataAdapter da = new SqlDataAdapter(cmd);
            DataTable dt = new DataTable();
            da.Fill(dt);
            ddldoctor.DataSource = dt;
            ddldoctor.DataTextField = "FULLNAME";
            ddldoctor.DataValueField = "F_DOC_ID";
            ddldoctor.DataBind();
            con.Close();
        }
    }

    private void LoadSpecialty()
    {
        using (SqlConnection con = new SqlConnection(connection))
        {
            // Clear existing items first
            ddltype.Items.Clear();

            con.Open();

            // Use parameters to avoid SQL injection
            SqlCommand cmd = new SqlCommand(@"SELECT DISTINCT DEGREE FROM DOCTOR_DB WHERE F_DOC_ID = @docID", con);
            cmd.Parameters.AddWithValue("@docID", lblid.Text);

            SqlDataAdapter da = new SqlDataAdapter(cmd);
            DataTable dt = new DataTable();
            da.Fill(dt);

            // Add the default item after clearing
            ddltype.Items.Add(new ListItem("--Select type--", ""));

            // Populate the dropdown with degrees for this doctor
            foreach (DataRow row in dt.Rows)
            {
                string degree = row["DEGREE"].ToString();
                ddltype.Items.Add(new ListItem(degree, degree));
            }

            con.Close();
        }
    }

    private void LoadAppointmentTime()
    {
        using (SqlConnection conn = new SqlConnection(connection))
        {
            conn.Open();

            // Clear existing items
            ddltime.Items.Clear();
            ddltime.Items.Add(new ListItem("--Select Time--", ""));

            // Only proceed if we have a valid doctor ID
            if (!string.IsNullOrEmpty(lblid.Text))
            {
                // Query to get time slots for the specified DateID
                SqlCommand cmd = new SqlCommand("SELECT TimeSlot FROM TimeSlots WHERE DateID = @DateID ORDER BY TimeSlot", conn);
                cmd.Parameters.AddWithValue("@DateID", lblid.Text);

                // Add each time slot to the dropdown
                using (SqlDataReader reader = cmd.ExecuteReader())
                {
                    while (reader.Read())
                    {
                        // Get the time value
                        string rawTimeValue = reader["TimeSlot"].ToString();

                        // Try parsing as DateTime first
                        DateTime timeValue;
                        if (DateTime.TryParse(rawTimeValue, out timeValue))
                        {
                            string formattedTime = timeValue.ToString("h:mm tt", System.Globalization.CultureInfo.InvariantCulture).ToUpper();
                            ddltime.Items.Add(new ListItem(formattedTime, formattedTime));
                        }
                        else
                        {
                            TimeSpan timeSpan;
                            if (TryParseTimeSpan(rawTimeValue, out timeSpan))
                            {
                                // Create a DateTime to format the TimeSpan
                                DateTime timeToFormat = DateTime.Today.Add(timeSpan);
                                string formattedTime = timeToFormat.ToString("h:mm tt", System.Globalization.CultureInfo.InvariantCulture).ToUpper();
                                ddltime.Items.Add(new ListItem(formattedTime, formattedTime));
                            }
                            else
                            {
                                // If it's already a formatted string or can't be parsed, use as is
                                ddltime.Items.Add(new ListItem(rawTimeValue, rawTimeValue));
                            }
                        }
                    }
                }
            }
            conn.Close();
        }
    }

    private bool TryParseTimeSpan(string value, out TimeSpan result)
    {
        result = TimeSpan.Zero;
        try
        {
            result = TimeSpan.Parse(value);
            return true;
        }
        catch
        {
            return false;
        }
    }

    private void DoctorInformationShow()
    {
        using (SqlConnection con = new SqlConnection(connection))
        {
            con.Open();

            string query = @"SELECT * FROM DOCTOR_DB WHERE F_DOC_ID = @id";

            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                cmd.Parameters.AddWithValue("@id", lblid.Text);
                SqlDataAdapter da = new SqlDataAdapter(cmd);
                DataTable dt = new DataTable();
                da.Fill(dt);

                if (dt.Rows.Count > 0)
                {
                    txtdocname.Text = dt.Rows[0]["FULLNAME"].ToString();
                    txtdocdegree.Text = dt.Rows[0]["DEGREE"].ToString();
                    txtdoctime.Text = dt.Rows[0]["AVAILABLE_TIME"].ToString();
                }
            }
            con.Close();
        }
    }

    private void DayShow()
    {
        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {
            con.Open();
            if (!string.IsNullOrEmpty(lblid.Text))
            {
                SqlCommand cmd = new SqlCommand(@"SELECT * FROM AVAILABLE_TIME_DB WHERE AVAILABLE_ID = @id", con);
                cmd.Parameters.AddWithValue("@id", lblid.Text);
                SqlDataAdapter da = new SqlDataAdapter(cmd);
                DataTable dt = new DataTable();
                da.Fill(dt);

                rptday.DataSource = dt;
                rptday.DataBind();
            }
            con.Close();
        }
    }

    private void ListViewTab()
    {
        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {
            con.Open();
            if (!string.IsNullOrEmpty(lblid.Text))
            {
                SqlCommand cmd = new SqlCommand(@"SELECT * FROM APPOINTMENT_DB WHERE DOCTOR_ID = @UserID AND STATUS = 'Approved'", con);
                cmd.Parameters.AddWithValue("@UserID", lblid.Text);
                SqlDataAdapter da = new SqlDataAdapter(cmd);
                DataTable dt = new DataTable();
                da.Fill(dt);

                if (dt.Rows.Count > 0)
                {
                    lblListViewVisible.Visible = false;
                    rptList.DataSource = dt;
                    rptList.DataBind();
                }
                else
                {
                    lblListViewVisible.Visible = true;
                    lblListViewVisible.Text = "No records found";
                }
            }
            else
            {
                lblListViewVisible.Visible = true;
                lblListViewVisible.Text = "No doctor selected";
            }
            con.Close();
        }
    }

    protected void ddldoctor_SelectedIndexChanged(object sender, EventArgs e)
    {
        if (ddldoctor.SelectedIndex > 0)
        {
            lblid.Text = ddldoctor.SelectedValue;
            DoctorInformationShow();
            LoadSpecialty();
            LoadAppointmentTime();
            DayShow();
            LoadEvents();
            ListViewTab();
        }
    }

    protected void ddltype_SelectedIndexChanged(object sender, EventArgs e)
    {
        // No need to reset lblid.Text here as that would change the doctor
        // Just ensure we have valid selections
        if (ddldoctor.SelectedIndex > 0)
        {
            LoadAppointmentTime();
        }
    }

    protected void ddltime_SelectedIndexChanged(object sender, EventArgs e)
    {
        // No need to change any IDs or reload dropdowns here
        // Time selection doesn't affect other selections
    }

    protected void btnsubmit_Click(object sender, EventArgs e)
    {
        using (SqlConnection con = new SqlConnection(connection))
        {
            con.Open();

            // First, validate that the user has made proper selections
            if (ddldoctor.SelectedIndex == 0)
            {
                ScriptManager.RegisterStartupScript(this, GetType(), "Alert", "alert('Please select a doctor.');", true);
                return;
            }

            if (ddltype.SelectedIndex == 0)
            {
                ScriptManager.RegisterStartupScript(this, GetType(), "Alert", "alert('Please select an appointment type.');", true);
                return;
            }

            if (ddltime.SelectedIndex == 0)
            {
                ScriptManager.RegisterStartupScript(this, GetType(), "Alert", "alert('Please select an appointment time.');", true);
                return;
            }

            if (string.IsNullOrEmpty(txtdate.Text))
            {
                ScriptManager.RegisterStartupScript(this, GetType(), "Alert", "alert('Please select an appointment date.');", true);
                return;
            }

            string query = @"SELECT * FROM APPOINTMENT_DB WHERE DOCTOR_ID = @DoctorId AND TIME = @SelectedTime AND DATE = @SelectedDate AND (STATUS = 'Approved' OR STATUS = 'Pending')";
            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                // Set parameters to check availability based on doctor, date and time
                cmd.Parameters.AddWithValue("@DoctorId", Convert.ToInt32(ddldoctor.SelectedValue));
                cmd.Parameters.AddWithValue("@SelectedTime", ddltime.SelectedItem.Text);
                cmd.Parameters.AddWithValue("@SelectedDate", txtdate.Text);

                SqlDataReader reader = cmd.ExecuteReader();
                if (reader.HasRows)
                {
                    // If rows are returned, the time is already taken
                    ScriptManager.RegisterStartupScript(this, GetType(), "Alert", "alert('The time you selected is already in use. Please choose a different time or date.');", true);
                    reader.Close();
                }
                else
                {
                    // If no rows are returned, the time is available for the selected doctor
                    reader.Close();
                    string query1 = @"INSERT INTO APPOINTMENT_DB (DOCTOR_ID, PATIENT_ID, DOCTOR, TIME, DATE, TYPE, STATUS, PATIENT_NAME) VALUES 
                    (@docID, @patientID, @doc, @time, @date, @type, @status, @ptname)";
                    using (SqlCommand cmd1 = new SqlCommand(query1, con))
                    {
                        cmd1.Parameters.AddWithValue("@docID", ddldoctor.SelectedValue);
                        cmd1.Parameters.AddWithValue("@patientID", Session["UserId"]);
                        cmd1.Parameters.AddWithValue("@doc", ddldoctor.SelectedItem.Text);
                        cmd1.Parameters.AddWithValue("@time", ddltime.SelectedItem.Text);
                        cmd1.Parameters.AddWithValue("@date", txtdate.Text);
                        cmd1.Parameters.AddWithValue("@type", ddltype.SelectedItem.Text);
                        cmd1.Parameters.AddWithValue("@ptname", Master.labeluname.Text);
                        cmd1.Parameters.AddWithValue("@status", "Pending");
                        cmd1.ExecuteNonQuery();
                        History();
                        // Confirmation message and redirect after successful submission
                        Response.Write("<script>alert('Appointment Submitted!'); window.location='My_Appointment.aspx';</script>");
                    }
                }
                con.Close();
            }
        }
    }

    private void LoadEvents()
    {
        using (SqlConnection conn = new SqlConnection(connection))
        {
            if (!string.IsNullOrEmpty(lblid.Text))
            {
                conn.Open();
                string query = @"SELECT * FROM APPOINTMENT_DB WHERE DOCTOR_ID = @UserID AND STATUS = 'Approved'";
                SqlCommand cmd = new SqlCommand(query, conn);
                cmd.Parameters.AddWithValue("@UserID", lblid.Text);

                SqlDataAdapter adapter = new SqlDataAdapter(cmd);
                eventTable = new DataTable();
                adapter.Fill(eventTable);
                cal1.VisibleDate = DateTime.Now; // Optional: Reset calendar view

                // Clear the calendar before binding
                cal1.SelectedDates.Clear();

                foreach (DataRow row in eventTable.Rows)
                {
                    // Assuming APPT_DATE is of type DateTime in your database
                    DateTime eventDate = Convert.ToDateTime(row["DATE"]);
                    cal1.SelectedDates.Add(eventDate); // Add the event date to the calendar's selected dates
                }
                conn.Close();
            }
        }
    }

    protected void cal1_DayRender(object sender, DayRenderEventArgs e)
    {
        if (eventTable != null)
        {
            foreach (DataRow row in eventTable.Rows)
            {
                DateTime eventDate = Convert.ToDateTime(row["DATE"]);
                if (eventDate == e.Day.Date)
                {
                    // Highlight the cell
                    e.Cell.BackColor = System.Drawing.Color.Aqua;
                    e.Cell.ForeColor = System.Drawing.Color.Black;
                    e.Cell.ToolTip = row["TIME"].ToString();
                }
            }
        }
        if (e.Day.Date < DateTime.Today)
        {
            e.Day.IsSelectable = false;
            e.Cell.ForeColor = System.Drawing.Color.Gray;
        }
    }
}