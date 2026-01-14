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

public partial class Patient : System.Web.UI.Page
{
    SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString);
    private Doctor_List doctor_list = new Doctor_List();
    protected void Page_Load(object sender, EventArgs e)
    {
        if (!IsPostBack)
        {
            GetData();
        }
    }

    private void GetData()
    {

        SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString);

        SqlCommand cmd = new SqlCommand(@"SELECT * FROM PATIENT_DB", con);
        SqlDataAdapter da = new SqlDataAdapter(cmd);
        DataSet ds = new DataSet();
        da.Fill(ds);

        if (ds.Tables[0].Rows.Count > 0)
        {
            patientlist.DataSource = ds;
            patientlist.DataBind();
            patientlist.UseAccessibleHeader = true;
            patientlist.HeaderRow.TableSection = TableRowSection.TableHeader;
        }
        else
        {
            ds.Tables[0].Rows.Add(ds.Tables[0].NewRow());
            patientlist.DataSource = ds;
            patientlist.DataBind();
            int columncount = patientlist.Rows[0].Cells.Count;
            patientlist.Rows[0].Cells.Clear();
            patientlist.Rows[0].Cells.Add(new TableCell());
            patientlist.Rows[0].Cells[0].ColumnSpan = columncount;
            patientlist.Rows[0].Cells[0].Text = "No matching records found";
            patientlist.Rows[0].Cells[0].CssClass = "text-center";
            patientlist.UseAccessibleHeader = true;
            patientlist.HeaderRow.TableSection = TableRowSection.TableHeader;
        }
    }

}