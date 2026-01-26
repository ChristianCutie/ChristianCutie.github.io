import React from "react";
import { Button } from "@mui/material";
import Sidebar from "../../components/layout/Sidebar.jsx";
import { useNavigate } from "react-router-dom";

const Dashboard = ({ setIsAuthenticated }) => {
  const navigate = useNavigate();
  const handleLogout = () => {
    setIsAuthenticated(false);
    navigate("/");
  };

  return (
    <>
      <div style={{ display: "flex" }}>
        <Sidebar onLogout={handleLogout} />

       <main style={{ padding: "20px", flex: 1 }}>
              <h1>Admin Dashboard</h1>
              <p>Welcome! You are logged in.</p>
        </main>
      </div>
    </>
  );
};

export default Dashboard;
