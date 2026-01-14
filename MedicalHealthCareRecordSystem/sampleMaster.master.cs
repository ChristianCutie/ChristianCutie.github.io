using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Web.Services;

public partial class sampleMaster : System.Web.UI.MasterPage
{
    protected void Page_Load(object sender, EventArgs e)
    {

    }
    [WebMethod]
    public static string GetNotifications()
    {
        // Example: Fetch notifications from database or session
        string notification = "🔔 You have a new message!";

        // You can fetch from a database instead
        // string notification = YourDatabase.GetLatestNotification();

        return notification;
    }
}

