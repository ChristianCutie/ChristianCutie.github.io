import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import "./Profile.css";
import ProfileEditModal from "../../components/ProfileEditModal";
import AdminLayout from "../../components/layout/Adminlayout";
import { PencilSquare } from "react-bootstrap-icons";
import api from "../../config/axios";
import { useAuth } from "../../context/AuthContext";
import "./../../assets/style/global.css";
import coverPhoto from "../../assets/images/cover_photo.jpg";
import { Toast, ToastContainer } from "react-bootstrap";

const Profile = ({ setIsAuth }) => {
  const { isAuth } = useAuth();
  const [profileData, setProfileData] = useState(null);
  const [showModal, setShowModal] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);
  const [toast, setShowToast] = useState({
    show: false,
    message: "",
    type: "",
  });
  const navigate = useNavigate();

  // Add this useEffect to handle redirection when not authenticated
  useEffect(() => {
    if (!isAuth) {
      if (setIsAuth) setIsAuth(false);
      navigate("/snl-hr-app");
      return;
    }
  }, [isAuth, navigate]);

  useEffect(() => {
    if (isAuth) {
      fetchProfileData();
    }
  }, [isAuth]);

  const fetchProfileData = async () => {
    try {
      setIsLoading(true);
      setError(null);

      // Fetch profile data from API
      const response = await api.get("/my-profile");

      if (response.data && response.data.isSuccess) {
        const userData = response.data.user;
        setProfileData({
          id: userData.id,
          employee_id: userData.employee_id,
          first_name: userData.first_name,
          middle_name: userData.middle_name || null,
          last_name: userData.last_name,
          suffix: userData.suffix || null, // Added from backend
          email: userData.email,
          phone: userData.phone || null,
          sex: userData.sex || "Male",
          date_of_birth: userData.date_of_birth || null,
          place_of_birth: userData.place_of_birth || null,
          blood_type: userData.blood_type || null,
          citizenship: userData.citizenship || null,
          civil_status: userData.civil_status || null,
          height_m: userData.height_m || null,
          weight_kg: userData.weight_kg || null,
          residential_address: userData.residential_address || null,
          residential_tel_no: userData.residential_tel_no || null,
          residential_zipcode: userData.residential_zipcode || null,
          permanent_address: userData.permanent_address || null,
          permanent_tel_no: userData.permanent_tel_no || null,
          permanent_zipcode: userData.permanent_zipcode || null,
          father_name: userData.father_name || null,
          mother_name: userData.mother_name || null,
          parents_address: userData.parents_address || null, // Added from backend
          spouse_name: userData.spouse_name || null,
          spouse_occupation: userData.spouse_occupation || null,
          spouse_employer: userData.spouse_employer || null,
          spouse_tel_no: userData.spouse_tel_no || null,
          spouse_business_address: userData.spouse_business_address || null,
          emergency_contact_name: userData.emergency_contact_name || null,
          emergency_contact_relation:
            userData.emergency_contact_relation || null,
          emergency_contact_number: userData.emergency_contact_number || null,
          elementary_school_name: userData.elementary_school_name || null,
          elementary_inclusive_dates:
            userData.elementary_inclusive_dates || null,
          elementary_degree_course: userData.elementary_degree_course || null,
          elementary_highest_level: userData.elementary_highest_level || null,
          elementary_year_graduated: userData.elementary_year_graduated || null,
          elementary_honors: userData.elementary_honors || null,
          secondary_school_name: userData.secondary_school_name || null,
          secondary_inclusive_dates: userData.secondary_inclusive_dates || null,
          secondary_degree_course: userData.secondary_degree_course || null,
          secondary_highest_level: userData.secondary_highest_level || null,
          secondary_year_graduated: userData.secondary_year_graduated || null,
          secondary_honors: userData.secondary_honors || null,
          college_school_name: userData.college_school_name || null,
          college_inclusive_dates: userData.college_inclusive_dates || null,
          college_degree_course: userData.college_degree_course || null,
          college_highest_level: userData.college_highest_level || null,
          college_year_graduated: userData.college_year_graduated || null,
          college_honors: userData.college_honors || null,
          vocational_school_name: userData.vocational_school_name || null,
          vocational_inclusive_dates:
            userData.vocational_inclusive_dates || null,
          vocational_degree_course: userData.vocational_degree_course || null,
          vocational_highest_level: userData.vocational_highest_level || null,
          vocational_year_graduated: userData.vocational_year_graduated || null,
          vocational_honors: userData.vocational_honors || null,
          graduate_school_name: userData.graduate_school_name || null,
          graduate_inclusive_dates: userData.graduate_inclusive_dates || null,
          graduate_degree_course: userData.graduate_degree_course || null,
          graduate_highest_level: userData.graduate_highest_level || null,
          graduate_year_graduated: userData.graduate_year_graduated || null,
          graduate_honors: userData.graduate_honors || null,
          sss_no: userData.sss_no || null,
          gsis_no: userData.gsis_no || null,
          pagibig_no: userData.pagibig_no || null,
          philhealth_no: userData.philhealth_no || null,
          tin_no: userData.tin_no || null,
          salary_mode: userData.salary_mode || null,
          face_id: userData.face_id || null,
          resume: userData.resume || null,
        });
      } else {
        setError(response.data?.message || "Failed to load profile");
      }
    } catch (err) {
      console.error("Error loading profile:", err);
      setError(
        err.response?.data?.message || err.message || "Failed to load profile",
      );
    } finally {
      setIsLoading(false);
    }
  };

  const handleUpdateProfile = async (updatedData) => {
    try {
      // Create a clean object without null/undefined values
      const cleanData = Object.keys(updatedData).reduce((acc, key) => {
        if (
          updatedData[key] !== null &&
          updatedData[key] !== undefined &&
          updatedData[key] !== ""
        ) {
          acc[key] = updatedData[key];
        }
        return acc;
      }, {});

      const response = await api.post(`/update-profile`, cleanData);

      if (response.data && response.data.isSuccess) {
        // Show success toast
        setShowToast({
          show: true,
          message: "Profile updated successfully!",
          type: "success",
        });
        // Refresh profile data after successful update
        fetchProfileData();
        setShowModal(false);
      } else {
        throw new Error(response.data?.message || "Failed to update profile");
      }
    } catch (err) {
      const errorMessage =
        err.response?.data?.message || err.message || "Error updating profile";
      console.error("Error updating profile:", err);
      // Show error toast
      setShowToast({
        show: true,
        message: errorMessage,
        type: "danger",
      });
    }
  };

  // Format date for display
  const formatDate = (dateString) => {
    if (!dateString) return "-";
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    } catch {
      return dateString;
    }
  };

  // Get initials for avatar
  const getInitials = () => {
    if (!profileData) return "";
    const first = profileData.first_name?.charAt(0) || "";
    const last = profileData.last_name?.charAt(0) || "";
    return (first + last).toUpperCase();
  };

  // Don't render anything if not authenticated (will redirect)
  if (!isAuth) {
    return null;
  }

  if (isLoading) {
    return (
      <AdminLayout setIsAuth={setIsAuth}>
        <div className="profile-loading">
          <div className="spinner-border text-primary" role="status">
            <span className="visually-hidden">Loading profile...</span>
          </div>
          <p>Loading profile information...</p>
        </div>
      </AdminLayout>
    );
  }

  if (error && !profileData) {
    return (
      <AdminLayout setIsAuth={setIsAuth}>
        <div className="profile-error alert alert-danger">
          <h4>Error Loading Profile</h4>
          <p>{error}</p>
          <button className="btn btn-primary mt-2" onClick={fetchProfileData}>
            Try Again
          </button>
        </div>
      </AdminLayout>
    );
  }

  if (!profileData) {
    return (
      <AdminLayout setIsAuth={setIsAuth}>
        <div className="profile-error alert alert-warning">
          No profile data available. Please contact administrator.
        </div>
      </AdminLayout>
    );
  }

  return (
    <AdminLayout setIsAuth={isAuth}>
      <div className="user-container">
        {/* Header Section */}
        <div className="user-header">
          <div className="user-cover">
            <img src={coverPhoto} alt="Cover" />
          </div>
          <div className="user-info-section">
            <div className="user-avatar">{getInitials()}</div>
            <div className="user-basic-info">
              <h1 className="user-name fw-bold">
                {profileData.first_name}{" "}
                {profileData.middle_name && profileData.middle_name + " "}
                {profileData.last_name}{" "}
                {profileData.suffix && profileData.suffix}
              </h1>
              <p className="user-employee-id">{profileData.employee_id}</p>
              <p className="user-email">{profileData.email}</p>
            </div>
            <button
              className="user-edit-btn"
              onClick={() => setShowModal(true)}
            >
              <PencilSquare className="me-2" size={16} /> Edit Profile
            </button>
          </div>
        </div>

        {/* Error banner */}
        {error && (
          <div
            className="alert alert-warning alert-dismissible fade show"
            role="alert"
          >
            <strong>Note:</strong> {error} Using fallback data.
            <button
              type="button"
              className="btn-close"
              onClick={() => setError(null)}
            ></button>
          </div>
        )}

        {/* Main Content */}
        <div className="user-content">
          {/* Personal Information */}
          <section className="user-section">
            <h2 className="user-section-title">Personal Information</h2>
            <div className="user-section-grid">
              <div className="user-info-item">
                <label>First Name:</label>
                <span>{profileData.first_name || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Middle Name:</label>
                <span>{profileData.middle_name || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Last Name:</label>
                <span>{profileData.last_name || "-"}</span>
              </div>
              {profileData.suffix && (
                <div className="user-info-item">
                  <label>Suffix:</label>
                  <span>{profileData.suffix}</span>
                </div>
              )}
              <div className="user-info-item">
                <label>Employee ID:</label>
                <span>{profileData.employee_id || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Email:</label>
                <span>{profileData.email || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Phone:</label>
                <span>{profileData.phone || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Gender:</label>
                <span>{profileData.sex || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Date of Birth:</label>
                <span>{formatDate(profileData.date_of_birth)}</span>
              </div>
              <div className="user-info-item">
                <label>Place of Birth:</label>
                <span>{profileData.place_of_birth || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Blood Type:</label>
                <span>{profileData.blood_type || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Citizenship:</label>
                <span>{profileData.citizenship || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Civil Status:</label>
                <span>{profileData.civil_status || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Height (m):</label>
                <span>{profileData.height_m || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Weight (kg):</label>
                <span>{profileData.weight_kg || "-"}</span>
              </div>
            </div>
          </section>

          {/* Contact Information */}
          <section className="user-section">
            <h2 className="user-section-title">Contact Information</h2>
            <div className="user-subsection">
              <h3>Residential Address</h3>
              <div className="user-section-grid">
                <div className="user-info-item user-full-width">
                  <label>Address:</label>
                  <span>{profileData.residential_address || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Telephone:</label>
                  <span>{profileData.residential_tel_no || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Zip Code:</label>
                  <span>{profileData.residential_zipcode || "-"}</span>
                </div>
              </div>
            </div>
            <div className="user-subsection">
              <h3>Permanent Address</h3>
              <div className="user-section-grid">
                <div className="user-info-item user-full-width">
                  <label>Address:</label>
                  <span>{profileData.permanent_address || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Telephone:</label>
                  <span>{profileData.permanent_tel_no || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Zip Code:</label>
                  <span>{profileData.permanent_zipcode || "-"}</span>
                </div>
              </div>
            </div>
          </section>

          {/* Family Information */}
          <section className="user-section">
            <h2 className="user-section-title">Family Information</h2>
            <div className="user-subsection">
              <h3>Parents</h3>
              <div className="user-section-grid">
                <div className="user-info-item">
                  <label>Father's Name:</label>
                  <span>{profileData.father_name || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Mother's Name:</label>
                  <span>{profileData.mother_name || "-"}</span>
                </div>
                <div className="user-info-item user-full-width">
                  <label>Parents' Address:</label>
                  <span>{profileData.parents_address || "-"}</span>
                </div>
              </div>
            </div>
            <div className="user-subsection">
              <h3>Spouse Information</h3>
              <div className="user-section-grid">
                <div className="user-info-item">
                  <label>Spouse Name:</label>
                  <span>{profileData.spouse_name || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Occupation:</label>
                  <span>{profileData.spouse_occupation || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Employer:</label>
                  <span>{profileData.spouse_employer || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Telephone:</label>
                  <span>{profileData.spouse_tel_no || "-"}</span>
                </div>
                <div className="user-info-item user-full-width">
                  <label>Business Address:</label>
                  <span>{profileData.spouse_business_address || "-"}</span>
                </div>
              </div>
            </div>
            <div className="user-subsection">
              <h3>Emergency Contact</h3>
              <div className="user-section-grid">
                <div className="user-info-item">
                  <label>Name:</label>
                  <span>{profileData.emergency_contact_name || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Relation:</label>
                  <span>{profileData.emergency_contact_relation || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Telephone:</label>
                  <span>{profileData.emergency_contact_number || "-"}</span>
                </div>
              </div>
            </div>
          </section>

          {/* Education Background */}
          <section className="user-section">
            <h2 className="user-section-title">Education Background</h2>
            <div className="user-subsection">
              <h3>Elementary</h3>
              <div className="user-section-grid">
                <div className="user-info-item">
                  <label>School Name:</label>
                  <span>{profileData.elementary_school_name || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Inclusive Dates:</label>
                  <span>{profileData.elementary_inclusive_dates || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Degree/Course:</label>
                  <span>{profileData.elementary_degree_course || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Highest Level:</label>
                  <span>{profileData.elementary_highest_level || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Year Graduated:</label>
                  <span>{profileData.elementary_year_graduated || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Honors:</label>
                  <span>{profileData.elementary_honors || "-"}</span>
                </div>
              </div>
            </div>

            <div className="user-subsection">
              <h3>Secondary</h3>
              <div className="user-section-grid">
                <div className="user-info-item">
                  <label>School Name:</label>
                  <span>{profileData.secondary_school_name || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Inclusive Dates:</label>
                  <span>{profileData.secondary_inclusive_dates || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Degree/Course:</label>
                  <span>{profileData.secondary_degree_course || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Highest Level:</label>
                  <span>{profileData.secondary_highest_level || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Year Graduated:</label>
                  <span>{profileData.secondary_year_graduated || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Honors:</label>
                  <span>{profileData.secondary_honors || "-"}</span>
                </div>
              </div>
            </div>

            <div className="user-subsection">
              <h3>College</h3>
              <div className="user-section-grid">
                <div className="user-info-item">
                  <label>School Name:</label>
                  <span>{profileData.college_school_name || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Inclusive Dates:</label>
                  <span>{profileData.college_inclusive_dates || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Degree/Course:</label>
                  <span>{profileData.college_degree_course || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Highest Level:</label>
                  <span>{profileData.college_highest_level || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Year Graduated:</label>
                  <span>{profileData.college_year_graduated || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Honors:</label>
                  <span>{profileData.college_honors || "-"}</span>
                </div>
              </div>
            </div>

            <div className="user-subsection">
              <h3>Vocational</h3>
              <div className="user-section-grid">
                <div className="user-info-item">
                  <label>School Name:</label>
                  <span>{profileData.vocational_school_name || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Inclusive Dates:</label>
                  <span>{profileData.vocational_inclusive_dates || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Degree/Course:</label>
                  <span>{profileData.vocational_degree_course || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Highest Level:</label>
                  <span>{profileData.vocational_highest_level || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Year Graduated:</label>
                  <span>{profileData.vocational_year_graduated || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Honors:</label>
                  <span>{profileData.vocational_honors || "-"}</span>
                </div>
              </div>
            </div>

            <div className="user-subsection">
              <h3>Graduate Studies</h3>
              <div className="user-section-grid">
                <div className="user-info-item">
                  <label>School Name:</label>
                  <span>{profileData.graduate_school_name || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Inclusive Dates:</label>
                  <span>{profileData.graduate_inclusive_dates || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Degree/Course:</label>
                  <span>{profileData.graduate_degree_course || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Highest Level:</label>
                  <span>{profileData.graduate_highest_level || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Year Graduated:</label>
                  <span>{profileData.graduate_year_graduated || "-"}</span>
                </div>
                <div className="user-info-item">
                  <label>Honors:</label>
                  <span>{profileData.graduate_honors || "-"}</span>
                </div>
              </div>
            </div>
          </section>

          {/* Government IDs */}
          <section className="user-section">
            <h2 className="user-section-title">Government IDs and Numbers</h2>
            <div className="user-section-grid">
              <div className="user-info-item">
                <label>SSS Number:</label>
                <span>{profileData.sss_no || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>GSIS Number:</label>
                <span>{profileData.gsis_no || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>PAG-IBIG Number:</label>
                <span>{profileData.pagibig_no || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>PhilHealth Number:</label>
                <span>{profileData.philhealth_no || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>TIN Number:</label>
                <span>{profileData.tin_no || "-"}</span>
              </div>
            </div>
          </section>

          {/* Other Information */}
          <section className="user-section">
            <h2 className="user-section-title">Other Information</h2>
            <div className="user-section-grid">
              <div className="user-info-item">
                <label>Salary Mode:</label>
                <span>{profileData.salary_mode || "-"}</span>
              </div>
              <div className="user-info-item">
                <label>Resume:</label>
                <span>
                  {profileData.resume ? (
                    <a
                      href={profileData.resume}
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      View Resume
                    </a>
                  ) : (
                    "-"
                  )}
                </span>
              </div>
              <div className="user-info-item">
                <label>Face ID:</label>
                <span>
                  {profileData.face_id ? "Registered" : "Not Registered"}
                </span>
              </div>
            </div>
          </section>
        </div>

        {/* Edit Modal */}
        {showModal && (
          <ProfileEditModal
            profileData={profileData}
            onClose={() => setShowModal(false)}
            onUpdate={handleUpdateProfile}
          />
        )}

        {/* Toast Container */}
        <ToastContainer position="top-end" className="p-3">
          <Toast
            show={toast.show}
            onClose={() => setShowToast({ ...toast, show: false })}
            delay={3000}
            autohide
            bg={toast.type}
          >
            <Toast.Body className={toast.type === "danger" ? "text-white" : ""}>
              {toast.message}
            </Toast.Body>
          </Toast>
        </ToastContainer>
      </div>
    </AdminLayout>
  );
};

export default Profile;
