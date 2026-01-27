import React, { useState } from "react";
import { Nav, Navbar, Container } from "react-bootstrap";
import { Link, useLocation, useNavigate } from "react-router-dom";
import "./Sidebar.css";
import {
  House,
  Cart3,
  People,
  Gear,
  BoxArrowRight,
  List,
  X,
} from "react-bootstrap-icons";

const Sidebar = ({ setIsAuth }) => {
  const [isOpen, setIsOpen] = useState(true);
  const location = useLocation();

  const menuItems = [
    {
      id: 1,
      title: "Dashboard",
      icon: House,
      path: "/admin/dashboard",
    },
    {
      id: 2,
      title: "Inventory",
      icon: Cart3,
      path: "/admin/inventory",
    },
    {
      id: 3,
      title: "Users",
      icon: People,
      path: "/admin/users",
    },
    {
      id: 4,
      title: "Settings",
      icon: Gear,
      path: "/settings",
    },
  ];

  const toggleSidebar = () => {
    setIsOpen(!isOpen);
  };

  const isActive = (path) => location.pathname === path;
  const navigate = useNavigate();

  const handleLogout = () => {
    console.log("Logging out");
    if (setIsAuth) setIsAuth(false);
    localStorage.removeItem("isAuth");
    navigate("/");
  };
  return (
    <>
      {/* Mobile Toggle Button */}
      <div className="mobile-toggle d-lg-none">
        <button
          className="btn btn-primary"
          onClick={toggleSidebar}
          aria-label="Toggle Sidebar"
        >
          {isOpen ? <X size={24} /> : <List size={24} />}
        </button>
      </div>

      {/* Sidebar */}
      <div className={`sidebar ${isOpen ? "open" : "closed"}`}>
        {/* Logo Section */}
        <div className="sidebar-header">
          <div className="logo-container">
            <div className="logo-icon">IC</div>
            <div className="logo-text">
              <h3>Inventory</h3>
              <p>Clinic</p>
            </div>
          </div>
        </div>

        {/* Navigation Menu */}
        <Nav className="sidebar-nav flex-column">
          {menuItems.map((item) => {
            const Icon = item.icon;
            return (
              <Nav.Link
                key={item.id}
                as={Link}
                to={item.path}
                className={`nav-item ${isActive(item.path) ? "active" : ""}`}
              >
                <span className="nav-icon">
                  <Icon size={20} />
                </span>
                <span className="nav-text">{item.title}</span>
              </Nav.Link>
            );
          })}
        </Nav>

        {/* Sidebar Footer */}
        <div className="sidebar-footer">
          <button
            className="btn btn-logout text-primary w-100"
            onClick={handleLogout}
          >
            <BoxArrowRight size={18} />
            <span>Logout</span>
          </button>
          <div className="user-info mt-3 pt-3 border-top">
            <div className="user-avatar">A</div>
            <div className="user-details">
              <p className="user-name">Admin User</p>
              <p className="user-email">admin@clinic.com</p>
            </div>
          </div>
        </div>
      </div>

      {/* Overlay for Mobile */}
      {isOpen && (
        <div
          className="sidebar-overlay d-lg-none"
          onClick={toggleSidebar}
        ></div>
      )}
    </>
  );
};

export default Sidebar;
