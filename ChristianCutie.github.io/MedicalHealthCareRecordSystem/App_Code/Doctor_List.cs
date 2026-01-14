using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Data;
using System.Data.SqlClient;
using System.Configuration;

/// <summary>
/// Summary description for Doctor_List
/// </summary>
public class Doctor_List 
{
    public int id { get; set; }
    public string FULLNAME { get; set; }
    public string DEGREE { get; set; }
    public string AVAILABLE_TIME { get; set; }
    public string EMAIL_ADDRESS { get; set; }
    public string PASSWORD { get; set; }
    public string STATUS { get; set; }


    public void InsertDataDoctor(Doctor_List li)
    {
        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {

            string query = @"INSERT INTO DOCTOR_DB (FULLNAME, DEGREE, AVAILABLE_TIME, EMAIL_ADDRESS, PASSWORD, STATUS) VALUES 
                    (@fn, @deg, @avtime, @email, @password, @status)";

            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                cmd.Parameters.AddWithValue("@fn", li.FULLNAME);
                cmd.Parameters.AddWithValue("@deg", li.DEGREE);
                cmd.Parameters.AddWithValue("@avtime", li.AVAILABLE_TIME);
                cmd.Parameters.AddWithValue("@email", li.EMAIL_ADDRESS);
                cmd.Parameters.AddWithValue("@password", li.PASSWORD);
                cmd.Parameters.AddWithValue("@status", li.STATUS);
                con.Open();
                cmd.ExecuteNonQuery();
                con.Close();
            }
        }
    }
}