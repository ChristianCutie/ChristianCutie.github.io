import React from 'react'
import Sidebar from '../../components/layout/Sidebar'
import './AdminLayout.css'

const AdminLayout = ({ children, setIsAuth }) => {
  return (
    <div className="admin-layout">
      <Sidebar setIsAuth={setIsAuth} />
      <div className="content-wrapper">
        {children}
      </div>
    </div>
  )
}

export default AdminLayout
