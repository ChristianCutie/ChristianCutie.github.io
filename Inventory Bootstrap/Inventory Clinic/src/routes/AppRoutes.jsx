import React from "react";
import { Routes, Route, Navigate } from "react-router";
import Login from "../pages/auth/Login.jsx";
import Dashboard from "../pages/dashboard/Dashboard.jsx";
import Inventory from "../pages/inventory/inventory.jsx";
import Users from "../pages/users/Users.jsx";
import { useState } from "react";

const AppRoutes = () => {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  return (
    <div>
      <Routes>
        <Route
          path="/"
          element={<Login setIsAuthenticated={setIsAuthenticated} />}
        />
        <Route
          path="/admin/dashboard"
          element={
            isAuthenticated ? (
              <Dashboard setIsAuthenticated={setIsAuthenticated} />
            ) : (
              <Navigate to="/" />
            )
          }
        />
        <Route
          path="/admin/inventory"
          element={
            isAuthenticated ? (
              <Inventory setIsAuthenticated={setIsAuthenticated} />
            ) : (
              <Navigate to="/" />
            )
          }
        />
        <Route
          path="/admin/users"
          element={
            isAuthenticated ? (
              <Users />
            ) : (
              <Navigate to="/" />
            )
          }
        />
        {/* Default Redirect */}
        <Route path="*" element={<Navigate to="/" />} />
      </Routes>
    </div>
  );
};

export default AppRoutes;
