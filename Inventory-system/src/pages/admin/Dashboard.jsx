import React from 'react'
import { Button } from "@mui/material";
import Sidebar from '../../components/layout/Sidebar.jsx';


const Dashboard = ({ setIsAuthenticated}) => {

  const handleLogout = () => {
    setIsAuthenticated(false);
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
  )
}


export default Dashboard;