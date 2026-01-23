import React from 'react'
import { useRoutes } from "react-router-dom";
import Login from '../pages/auth/login';
import Dashboard from '../pages/admin/Dashboard';

const AppRoutes = () => {

    const routes = useRoutes([
       {path: '/', element: <Login />},
       {path: '/dashboard', element: <Dashboard />},
    ]);
  return routes;
}

export default AppRoutes
