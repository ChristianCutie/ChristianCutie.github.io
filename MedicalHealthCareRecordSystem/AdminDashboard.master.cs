using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;

public partial class DashboardMasterPage : System.Web.UI.MasterPage
{
    protected void Page_Load(object sender, EventArgs e)
    {
        lbldatenow.Text = DateTime.Now.ToString("MMM dd, yyyy");
    }
    protected void lnklogout_Click(object sender, EventArgs e)
    {
        HttpCookie myCookie = new HttpCookie("login");
        myCookie.Expires = DateTime.Now.AddDays(-1d);
        Response.Cookies.Add(myCookie);
        Session.Abandon();
        Session.Clear();
        Session.RemoveAll();
        Response.Redirect("~/login.aspx");
    }

    protected void btnLogout_Click(object sender, EventArgs e)
    {
        Session.Clear();
        Session.Abandon();
        Response.Redirect("../login.aspx");
    }
}
