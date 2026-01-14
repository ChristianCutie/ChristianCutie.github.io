using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Configuration;
using System.Data;
using System.Data.SqlClient;

/// <summary>
/// Summary description for History
/// </summary>
public class History
{
    // Properties that match what you're trying to use
    public int PATIENT_ID { get; set; }
    public int DOCTOR_ID { get; set; }
    public string DOCTOR { get; set; }
    public string PATIENT_NAME { get; set; }
    public string SPECIALTY { get; set; }
    public string TIME { get; set; }
    public string DATE { get; set; }
    public string STATUS { get; set; }
    public DateTime DATE_ADDED { get; set; }

    // Connection string
    private string connection = ConfigurationManager.ConnectionStrings["conn"].ConnectionString;

    // Method to insert history records
    public bool InsertHistory(History h_list)
    {
        try
        {
            using (SqlConnection con = new SqlConnection(connection))
            {
                con.Open();
                string query = @"INSERT INTO HISTORY_TB (PATIENT_ID, DOCTOR_ID, DOCTOR, PATIENT_NAME, SPECIALTY, TIME, DATE, STATUS, DATE_ADDED) 
                            VALUES (@PATIENT_ID, @DOCTOR_ID, @DOCTOR, @PATIENT_NAME, @SPECIALTY, @TIME, @DATE, @STATUS, @DATEADDED)";

                using (SqlCommand cmd = new SqlCommand(query, con))
                {
                    cmd.Parameters.AddWithValue("@PATIENT_ID", h_list.PATIENT_ID);
                    cmd.Parameters.AddWithValue("@DOCTOR_ID", h_list.DOCTOR_ID);
                    cmd.Parameters.AddWithValue("@DOCTOR", h_list.DOCTOR);
                    cmd.Parameters.AddWithValue("@PATIENT_NAME", h_list.PATIENT_NAME);
                    cmd.Parameters.AddWithValue("@SPECIALTY", h_list.SPECIALTY);
                    cmd.Parameters.AddWithValue("@TIME", h_list.TIME);
                    cmd.Parameters.AddWithValue("@DATE", h_list.DATE);
                    cmd.Parameters.AddWithValue("@STATUS", h_list.STATUS);
                    cmd.Parameters.AddWithValue("@DATEADDED", h_list.DATE_ADDED);

                    cmd.ExecuteNonQuery();
                    return true;
                }
            }
        }
        catch (Exception)
        {

            return false;
        }

       
    }
}