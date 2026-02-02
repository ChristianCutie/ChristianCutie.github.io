import React from 'react'
import { Routes, Route, Navigate } from 'react-router-dom'
import Login from '../pages/auth/Login.jsx'
import Dashboard from '../pages/dashboard/dashboard.jsx'
import Leave from '../pages/leave/Leave.jsx'
import Attendance from '../pages/attendance/Attendance.jsx'
import Loan from '../pages/loan/Loan.jsx'

const AppRoutes = () => {
  return (
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/leave" element={<Leave />} />
        <Route path="/attendance" element={<Attendance />} />
        <Route path="/loan" element={<Loan />} />
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
  )
}

export default AppRoutes
