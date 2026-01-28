import { Routes, Route, Navigate } from "react-router-dom";
import Login from "../pages/auth/Login";
import Dashboard from "../pages/dashboard/Dashboard";
import Users from "../pages/users/Users";
import Inventory from "../pages/inventory/inventory";
import { useEffect } from "react";

const AppRoutes = ({ isAuth, setIsAuth }) => {
   useEffect(() => {
    const auth = localStorage.getItem("isAuth");
    if (auth === "true") {
      setIsAuth(true);
    } else {
      setIsAuth(false);
    }
  }, [setIsAuth]);
  
  return (
    <Routes>
      <Route
        path="/"
        element={
          isAuth ? <Navigate to="/admin/dashboard" replace /> : <Login setIsAuth={setIsAuth} />
        }
      />

      <Route
        path="/admin/dashboard"
        element={isAuth ? <Dashboard setIsAuth={setIsAuth} /> : <Navigate to="/" replace />}
      />

      <Route
        path="/admin/inventory"
        element={isAuth ? <Inventory setIsAuth={setIsAuth}  /> : <Navigate to="/" replace />}
      />

      <Route
        path="/admin/users"
        element={isAuth ? <Users setIsAuth={setIsAuth}  /> : <Navigate to="/" replace />}
      />
    </Routes>
  );
};

export default AppRoutes;
