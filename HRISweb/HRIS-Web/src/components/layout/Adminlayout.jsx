import React, { useState } from "react";
import { Button, Dropdown } from "react-bootstrap";
import { List, KeyFill, BoxArrowRight, PersonCircle } from "react-bootstrap-icons";
import Sidebar from "./Sidebar.jsx";
import "./AdminLayout.css";

/**
 * AdminLayout is a higher-order component that wraps the main content
 * of the application with a sidebar and a top navigation bar.
 * It provides a simple way to manage the layout of the application.
 * The component accepts a single prop, "children", which is the main content
 * of the application.
 *
 * @param { React.ReactNode } children - The main content of the application.
 * @returns { React.ReactElement } A React element representing the layout of the application.
 */


const AdminLayout = ({ children }) => {
  const [sidebarShow, setSidebarShow] = useState(false);

  const handleLogout = () => {
    console.log("Logout clicked");
    // Add your logout logic here
  };

  const handleChangePassword = () => {
    console.log("Change password clicked");
    // Add your change password logic here
  };

  return (
    <div className="admin-layout">
      {/* Sidebar */}
      <Sidebar show={sidebarShow} handleClose={() => setSidebarShow(false)} />

      {/* Main Content */}
      <div className="admin-content">
        {/* Top Navigation Bar */}
        <nav className="admin-navbar">
          <div className="navbar-content">
            <Button
              variant="link"
              className="navbar-toggle"
              onClick={() => setSidebarShow(true)}
            >
              <List size={24} />
            </Button>
            <h5 className="navbar-title">HRIS System</h5>
            <div className="navbar-profile">
              <p className="profile-name">Christian Buenaflor</p>
              <Dropdown className="profile-dropdown" align="end">
                <Dropdown.Toggle
                  variant="link"
                  id="profile-dropdown"
                  className="profile-avatar-toggle"
                >
                  <div className="profile-avatar">
                    <PersonCircle size={24} />
                  </div>
                </Dropdown.Toggle>

                <Dropdown.Menu className="profile-dropdown-menu">
                  <Dropdown.Item
                    href="#"
                    onClick={(e) => {
                      e.preventDefault();
                      handleChangePassword();
                    }}
                    className="dropdown-item-custom"
                  >
                    <KeyFill size={16} />
                    <span>Change Password</span>
                  </Dropdown.Item>
                  <Dropdown.Divider />
                  <Dropdown.Item
                    href="#"
                    onClick={(e) => {
                      e.preventDefault();
                      handleLogout();
                    }}
                    className="dropdown-item-custom dropdown-item-logout"
                  >
                    <BoxArrowRight size={16} />
                    <span>Logout</span>
                  </Dropdown.Item>
                </Dropdown.Menu>
              </Dropdown>
            </div>
          </div>
        </nav>

        {/* Main Content Area */}
        <div className="admin-main">{children}</div>
      </div>
    </div>
  );
};

export default AdminLayout;
