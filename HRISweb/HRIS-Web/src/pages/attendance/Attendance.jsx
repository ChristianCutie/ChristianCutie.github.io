import React, { useState, useEffect } from 'react'
import { Container, Row, Col, Card, Button, Badge, Table } from 'react-bootstrap'
import AdminLayout from '../../components/layout/Adminlayout'
import {
  Clock,
  Calendar,
  CheckCircle,
  ClockFill,
  PauseCircle,
  DoorOpen
} from 'react-bootstrap-icons'
import './Attendance.css'

const Attendance = () => {
  const [currentTime, setCurrentTime] = useState(new Date())
  const [isCheckedIn, setIsCheckedIn] = useState(true)

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentTime(new Date())
    }, 1000)
    return () => clearInterval(timer)
  }, [])

  const formatTime = (date) => {
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })
  }

  const formatDate = (date) => {
    return date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
  }

  const recentAttendance = [
    { id: 1, date: 'Feb 2, 2026', status: 'Present', time: '09:29 PM', hours: '0.00 hrs' }
  ]

  return (
    <AdminLayout>
      <Container fluid className="attendance-container">
        {/* Header Section */}
        <div className="attendance-header">
          <h2 className="attendance-title">Attendance</h2>
        </div>

        {/* Clock Section */}
        <Card className="clock-card">
          <Card.Body className="clock-body">
            <div className="date-display">
              <p className="date-text">{formatDate(currentTime)}</p>
            </div>

            <div className="time-display">
              <h1 className="current-time">{formatTime(currentTime)}</h1>
              <div className="status-indicator">
                <span className="status-dot"></span>
                <span className="status-text">Checked In</span>
              </div>
            </div>

            <Row className="clock-info mt-4">
              <Col md={6} sm={12} className="info-box">
                <p className="info-label">Clock In Time</p>
                <h5 className="info-value">09:29 PM</h5>
              </Col>
              <Col md={6} sm={12} className="info-box">
                <p className="info-label">Hours Today</p>
                <h5 className="info-value">0.00 hrs</h5>
              </Col>
            </Row>

            <Row className="clock-info">
              <Col md={6} sm={12} className="info-box">
                <p className="info-label">Status</p>
                <Badge className="status-badge-working">Working</Badge>
              </Col>
              <Col md={6} sm={12}></Col>
            </Row>

            <div className="action-buttons mt-4">
              <Button className="btn-start-break">
                <PauseCircle size={18} className="me-2" />
                Start Break
              </Button>
              <Button className="btn-clock-out">
                <DoorOpen size={18} className="me-2" />
                Clock Out
              </Button>
            </div>
          </Card.Body>
        </Card>

        {/* Statistics Section */}
        <Row className="stats-section mt-4">
          <Col lg={3} md={6} sm={6} className="mb-3">
            <div className="stat-box">
              <div className="stat-icon">
                <ClockFill size={24} />
              </div>
              <p className="stat-label">This Week</p>
              <h5 className="stat-value">0.0 hrs</h5>
            </div>
          </Col>
          <Col lg={3} md={6} sm={6} className="mb-3">
            <div className="stat-box">
              <div className="stat-icon">
                <Calendar size={24} />
              </div>
              <p className="stat-label">This Month</p>
              <h5 className="stat-value">0.0 hrs</h5>
            </div>
          </Col>
          <Col lg={3} md={6} sm={6} className="mb-3">
            <div className="stat-box">
              <div className="stat-icon">
                <Clock size={24} />
              </div>
              <p className="stat-label">Attendance</p>
              <h5 className="stat-value">1%</h5>
            </div>
          </Col>
          <Col lg={3} md={6} sm={6} className="mb-3">
            <div className="stat-box">
              <div className="stat-icon">
                <CheckCircle size={24} />
              </div>
              <p className="stat-label">On Time</p>
              <h5 className="stat-value">95%</h5>
            </div>
          </Col>
        </Row>

        {/* Recent Attendance Section */}
        <Card className="recent-attendance-card mt-5">
          <Card.Header className="card-header-custom">
            <h5 className="card-title">Recent Attendance</h5>
            <p className="card-subtitle">Your recent attendance records</p>
          </Card.Header>
          <Card.Body>
            <div className="table-responsive">
              <Table borderless className="attendance-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Time</th>
                    <th>Hours</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  {recentAttendance.map((record) => (
                    <tr key={record.id}>
                      <td>
                        <strong>{record.date}</strong>
                      </td>
                      <td>
                        <Badge className="status-badge-present">{record.status}</Badge>
                      </td>
                      <td>{record.time} - ...</td>
                      <td>{record.hours}</td>
                      <td>
                        <Button className="btn-action btn-present">Present</Button>
                        <Button className="btn-action btn-adjust">Adjust</Button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </Table>
            </div>
          </Card.Body>
        </Card>
      </Container>
    </AdminLayout>
  )
}

export default Attendance
