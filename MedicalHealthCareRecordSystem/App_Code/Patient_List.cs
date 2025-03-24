using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Configuration;
using System.Data;
using System.Data.SqlClient;


/// <summary>
/// Summary description for Patient_List
/// </summary>
public class Patient_List
{
    public int id { get; set; }
    public int LOGIN_ID { get; set; }
    public string FULLNAME { get; set; }
    public string ADDRESS { get; set; }
    public string CONTACT { get; set; }
    public string BDAY { get; set; }
    public string NATIONALITY { get; set; }
    public string GENDER { get; set; }
    public int AGE { get; set; }

    public void InsertData(Patient_List li)
    {
        using (SqlConnection con = new SqlConnection(ConfigurationManager.ConnectionStrings["conn"].ConnectionString))
        {

            string query = @"INSERT INTO PATIENT_DB (FULLNAME, ADDRESS, CONTACT,BDAY, NATIONALITY, AGE, GENDER, LOGIN_ID) VALUES 
                    (@fn, @add, @contact, @bday, @nationality, @age,@gender, @loginid)";

            using (SqlCommand cmd = new SqlCommand(query, con))
            {
                cmd.Parameters.AddWithValue("@fn", li.FULLNAME);
                cmd.Parameters.AddWithValue("@add", li.ADDRESS);
                cmd.Parameters.AddWithValue("@contact", li.CONTACT);
                cmd.Parameters.AddWithValue("@bday", li.BDAY);
                cmd.Parameters.AddWithValue("@nationality", li.NATIONALITY);
                cmd.Parameters.AddWithValue("@age", li.AGE);
                cmd.Parameters.AddWithValue("@gender", li.GENDER);
                cmd.Parameters.AddWithValue("@loginid", li.LOGIN_ID);
                con.Open();
                cmd.ExecuteNonQuery();
                con.Close();
            }
        }
    }
}
