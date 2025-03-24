using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Data;
using System.Data.SqlClient;
using System.Configuration;

public partial class Doctor_Dashboard : System.Web.UI.MasterPage
{
    protected void Page_Load(object sender, EventArgs e)
    {
        if (Session["UserId"] == null)
        {
            Response.Redirect("login.aspx");
        }

        lbldatetoday.Text = DateTime.Now.ToString("MMM dd, yyyy");
    }
    public Label labeluname
    {
        get
        {
            return this.lblmastername;
        }
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
}
