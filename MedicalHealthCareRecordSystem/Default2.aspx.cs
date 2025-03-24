using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data.SqlClient;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;

public partial class Default2 : System.Web.UI.Page
{
    protected void Page_Load(object sender, EventArgs e)
    {
        if (!IsPostBack)
        {
            // Populate both dropdown lists with all possible times
            BindAllTimeIntervalsToDropdowns();
        }

    }
    public class TimeItem
    {
        public TimeSpan TimeValue { get; set; }
        public string DisplayText { get; set; }
        public int TotalMinutes { get; set; }
    }
    private string connectionString = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;

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
        ddlTimeIn.DataSource = timeIntervals;
        ddlTimeIn.DataTextField = "DisplayText";
        ddlTimeIn.DataValueField = "TotalMinutes";
        ddlTimeIn.DataBind();

        ddlTimeOut.DataSource = timeIntervals;
        ddlTimeOut.DataTextField = "DisplayText";
        ddlTimeOut.DataValueField = "TotalMinutes";
        ddlTimeOut.DataBind();

        // Set default values (8:00 AM to 5:00 PM)
        ddlTimeIn.Items.FindByText("8:00 AM").Selected = true;
        ddlTimeOut.Items.FindByText("5:00 PM").Selected = true;
    }

    protected void btnSaveIntervals_Click(object sender, EventArgs e)
    {
        try
        {
            // Clear any previous messages
            lblMessage.Text = "";
            lblError.Text = "";

            // Get selected time range
            int startMinutes = Convert.ToInt32(ddlTimeIn.SelectedValue);
            int endMinutes = Convert.ToInt32(ddlTimeOut.SelectedValue);

            // Get DateID from textbox
            int dateId = Convert.ToInt32(txtDateID.Text);

            // Validate time range
            if (endMinutes <= startMinutes)
            {
                lblError.Text = "Time Out must be after Time In";
                return;
            }

            // Calculate number of 30-minute intervals
            int intervalCount = (endMinutes - startMinutes) / 30;

            // Create a list to display the generated time slots
            List<string> displayTimeSlots = new List<string>();

            // Database connection
            using (SqlConnection conn = new SqlConnection(connectionString))
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

            // Display the generated time slots
            lstTimeSlots.DataSource = displayTimeSlots;
            lstTimeSlots.DataBind();

            lblMessage.Text = "Time slots generated and saved successfully!";
        }
        catch (Exception ex)
        {
            lblError.Text = "Error: " + ex.Message;
        }
    }
}