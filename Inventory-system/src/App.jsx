import React from 'react'
import AppRoutes from './routes/AppRoutes'
import './index.css' 
import {Link} from 'react-router-dom'
import Sidebar from './components/layout/Sidebar.jsx'


const App = () => {
  return (
    <>
    <div className=''></div>
      <AppRoutes />
      <Sidebar />
    </>
  )
}

export default App
