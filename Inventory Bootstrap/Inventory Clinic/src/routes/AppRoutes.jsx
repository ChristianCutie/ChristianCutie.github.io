import { Routes, Route, Navigate } from "react-router-dom";
import Login from "../pages/auth/Login";
import Dashboard from "../pages/dashboard/Dashboard";
import Users from "../pages/users/Users";
import Inventory from "../pages/inventory/inventory";
import { useEffect } from "react";

const AppRoutes = ({ isAuth, setIsAuth }) => {
   useEffect(() => {
    const auth = localStorage.getItem("isAuth");
    if (auth === "true") setIsAuth(true);
  }, [setIsAuth]);
  return (
    <Routes>
      <Route
        path="/"
        element={
          isAuth ? <Navigate to="/admin/dashboard" /> : <Login setIsAuth={setIsAuth} />
        }
      />

      <Route
        path="/admin/dashboard"
        element={isAuth ? <Dashboard setIsAuth={setIsAuth} /> : <Navigate to="/" />}
      />

      <Route
        path="/admin/inventory"
        element={isAuth ? <Inventory setIsAuth={setIsAuth}  /> : <Navigate to="/" />}
      />

      <Route
        path="/admin/users"
        element={isAuth ? <Users setIsAuth={setIsAuth}  /> : <Navigate to="/" />}
      />
    </Routes>
  );
};

export default AppRoutes;
