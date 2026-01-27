import React from "react";
import "bootstrap/dist/css/bootstrap.min.css";
import AppRoutes from "./routes/AppRoutes";
import { useState, useEffect } from "react";
import { Routes, Route, Navigate } from "react-router";
import Login from "./pages/auth/Login.jsx";
import Dashboard from "./pages/dashboard/Dashboard.jsx";
import Inventory from "./pages/inventory/inventory.jsx";
import Users from "./pages/users/Users.jsx";

const App = () => {
  const [isAuth, setIsAuth] = useState(false);

  useEffect(() => {
    const auth = localStorage.getItem("isAuth");
    if (auth === "true") {
      setIsAuth(true);
    }
  }, []);
  return (
    <>
      <Routes>
        <Route
          path="/"
          element={
            isAuth ? (
              <Navigate to="/admin/dashboard" />
            ) : (
              <Login setIsAuth={setIsAuth} />
            )
          }
        />
        <Route
          path="/admin/inventory"
          element={isAuth ? <Inventory setIsAuth={setIsAuth} /> : <Navigate to="/" />}
        />
        <Route
          path="/admin/dashboard"
          element={isAuth ? <Dashboard setIsAuth={setIsAuth} /> : <Navigate to="/" />}
        /><Route
          path="/admin/users"
          element={isAuth ? <Users setIsAuth={setIsAuth} /> : <Navigate to="/" />}
        />

      </Routes>
    </>
  );
};

export default App;
