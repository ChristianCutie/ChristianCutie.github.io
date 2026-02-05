import React, { useState, useEffect } from "react";
import {
  Container,
  Row,
  Col,
  Card,
  Button,
  Badge,
  Table,
  Modal,
  Form,
  Toast,
  ToastContainer,
} from "react-bootstrap";
import AdminLayout from "../../components/layout/Adminlayout";
import {
  Clock,
  Calendar,
  CheckCircle,
  ClockFill,
  DoorOpen,
  Lightbulb,
} from "react-bootstrap-icons";
import api from "../../config/axios.js";
import "./Attendance.css";
import "../../assets/style/global.css";
import { useRef } from "react";
import { useAuth } from "../../context/AuthContext.jsx";

const Attendance = ({ setIsAuth }) => {
  const { isAuth } = useAuth();
  const [isLoading, setIsLoading] = useState(true);
  const hasFetched = useRef(false);
  const [currentTime, setCurrentTime] = useState(new Date());
  const [todayRecord, setTodayRecord] = useState(null);
  const [recentAttendance, setRecentAttendance] = useState([]);
  const [stats, setStats] = useState({
    thisWeekHours: 0,
    thisMonthHours: 0,
    attendanceRate: 0,
    onTimeRate: 0,
  });
  const [showAdjustModal, setShowAdjustModal] = useState(false);
  const [selectedRecord, setSelectedRecord] = useState(null);
  const [adjustmentForm, setAdjustmentForm] = useState({
    adjustedClockIn: "",
    adjustedClockOut: "",
    reason: "",
  });
  const [toasts, setToasts] = useState([]);

  /* =======================
     LIVE CLOCK
  ======================== */
  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentTime(new Date());
    }, 1000);
    return () => clearInterval(timer);
  }, []);

  /* =======================
     FETCH MY ATTENDANCE
  ======================== */
  const fetchAttendance = async () => {
    try {
      setIsLoading(true);
      const res = await api.get("/my-attendance");

      setTodayRecord(res.data.todayRecord);
      setRecentAttendance(res.data.recentAttendance);
      setStats({
        thisWeekHours: res.data.thisWeekHours,
        thisMonthHours: res.data.thisMonthHours,
        attendanceRate: res.data.attendanceRate,
        onTimeRate: res.data.onTimeRate,
      });
    } catch (error) {
      console.error("Error fetching attendance", error);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    if (hasFetched.current) return;
    hasFetched.current = true;
    fetchAttendance();
  }, []);

  /* =======================
     TOAST FUNCTIONS
  ======================== */
  const showToast = (message, type = "success") => {
    const id = Date.now();
    const newToast = {
      id,
      message,
      type,
      show: true,
    };

    setToasts((prevToasts) => [...prevToasts, newToast]);

    // Auto remove toast after 5 seconds
    setTimeout(() => {
      removeToast(id);
    }, 5000);
  };

  const removeToast = (id) => {
    setToasts((prevToasts) =>
      prevToasts.map((toast) =>
        toast.id === id ? { ...toast, show: false } : toast,
      ),
    );

    // Remove from array after fade out animation
    setTimeout(() => {
      setToasts((prevToasts) => prevToasts.filter((toast) => toast.id !== id));
    }, 300);
  };

  /* =======================
     HELPERS
  ======================== */
  const formatTime = (date) =>
    date.toLocaleTimeString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
      hour12: true,
    });

  const formatTimeForInput = (date) => {
    if (!date) return "";
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    return `${hours}:${minutes}`;
  };

  const formatDate = (date) =>
    date.toLocaleDateString("en-US", {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    });

  const calculateHoursToday = () => {
    if (!todayRecord?.clock_in) return "0.00";

    const start = new Date(todayRecord.clock_in);
    const end = todayRecord.clock_out
      ? new Date(todayRecord.clock_out)
      : currentTime;

    const diff = (end - start) / 3600000;
    return diff.toFixed(2);
  };

  const isCheckedIn = todayRecord && !todayRecord.clock_out;

  /* =======================
     CLOCK IN / CLOCK OUT
  ======================== */
  const handleClockAction = async () => {
    try {
      if (isCheckedIn) {
        // Clock Out
        const res = await api.post("/attendance/clock-out");
        setTodayRecord(res.data.attendance);
        showToast("Successfully clocked out!", "success");
      } else {
        // Clock In
        const res = await api.post("/attendance/clock-in");
        setTodayRecord(res.data.attendance);
        showToast("Successfully clocked in!", "success");
      }
      // Refetch all attendance data to update recent attendance
      await fetchAttendance();
    } catch (error) {
      console.error("Error clocking in/out:", error);
      showToast(
        error.response?.data?.message ||
          "Error clocking in/out. Please try again.",
        "danger",
      );
    }
  };

  /* =======================
     DTR ADJUSTMENT MODAL
  ======================== */
  const handleOpenAdjustModal = (record) => {
    setSelectedRecord(record);
    setAdjustmentForm({
      adjustedClockIn: record.clock_in
        ? formatTimeForInput(new Date(record.clock_in))
        : "",
      adjustedClockOut: record.clock_out
        ? formatTimeForInput(new Date(record.clock_out))
        : "",
      reason: "",
    });
    setShowAdjustModal(true);
  };

  const handleCloseAdjustModal = () => {
    setShowAdjustModal(false);
    setSelectedRecord(null);
    setAdjustmentForm({
      adjustedClockIn: "",
      adjustedClockOut: "",
      reason: "",
    });
  };

  const handleAdjustmentFormChange = (e) => {
    const { name, value } = e.target;
    setAdjustmentForm((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmitAdjustment = async () => {
    try {
      const recordDate = new Date(selectedRecord.clock_in)
        .toISOString()
        .split("T")[0];

      const payload = {
        adjusted_clock_in: adjustmentForm.adjustedClockIn
          ? `${recordDate}T${adjustmentForm.adjustedClockIn}:00Z`
          : null,
        adjusted_clock_out: adjustmentForm.adjustedClockOut
          ? `${recordDate}T${adjustmentForm.adjustedClockOut}:00Z`
          : null,
        reason: adjustmentForm.reason,
      };

      await api.post(`/request/adjustment/${selectedRecord.id}`, payload);

      // Show success toast
      showToast("Attendance adjustment submitted successfully!", "success");

      // Close modal and refresh data
      handleCloseAdjustModal();
      await fetchAttendance();
    } catch (error) {
      // Show error toast
      showToast(
        error.response?.data?.message ||
          "Error submitting adjustment request. Please try again.",
        "danger",
      );

      // Close modal but keep toast showing
      handleCloseAdjustModal();
    }
  };

  if (!isAuth) {
    return null;
  }
  if (isLoading) {
    return (
      <AdminLayout setIsAuth={setIsAuth}>
        <div className="profile-loading">
          <div className="spinner-border text-primary" role="status">
            <span className="visually-hidden">Loading attendance...</span>
          </div>
          <p>Loading attendance information...</p>
        </div>
      </AdminLayout>
    );
  }
  /* =======================
     UI
  ======================== */

  return (
    <AdminLayout setIsAuth={isAuth}>
      <Container fluid className="attendance-container">
        {/* Header */}
        <div className="attendance-header">
          <h2 className="attendance-title">Attendance</h2>
        </div>

        {/* Clock Card */}
        <Card className="clock-card">
          <Card.Body className="clock-body">
            <div className="date-display">
              <p className="date-text">{formatDate(currentTime)}</p>
            </div>

            <div className="time-display">
              <h1 className="current-time">{formatTime(currentTime)}</h1>
              <div className="status-indicator">
                <span className={`status-dot ${isCheckedIn ? "active" : ""}`} />
                <span className="status-text">
                  {isCheckedIn ? "Checked In" : "Not Checked In"}
                </span>
              </div>
            </div>

            <Row className="clock-info mt-4">
              <Col md={6} sm={12} className="info-box">
                <p className="info-label">Clock In Time</p>
                <h5 className="info-value">
                  {todayRecord?.clock_in
                    ? formatTime(new Date(todayRecord.clock_in))
                    : "--:--"}
                </h5>
              </Col>

              <Col md={6} sm={12} className="info-box">
                <p className="info-label">Hours Today</p>
                <h5 className="info-value">{calculateHoursToday()} hrs</h5>
              </Col>
            </Row>

            <Row className="clock-info">
              <Col md={6} sm={12} className="info-box">
                <p className="info-label">Status</p>
                <Badge
                  className={
                    isCheckedIn ? "status-badge-working" : "status-badge-off"
                  }
                >
                  {isCheckedIn ? "Working" : "Off Duty"}
                </Badge>
              </Col>
            </Row>

            <div className="action-buttons mt-4">
              {isCheckedIn ? (
                <Button className="btn-clock-out" onClick={handleClockAction}>
                  <DoorOpen size={18} className="me-2" />
                  Clock Out
                </Button>
              ) : (
                <Button className="btn-start-break" onClick={handleClockAction}>
                  <Clock size={18} className="me-2" />
                  Clock In
                </Button>
              )}
            </div>
          </Card.Body>
        </Card>

        {/* Stats */}
        <Row className="stats-section mt-4">
          <Col lg={3} md={6} sm={6} className="mb-3">
            <div className="stat-box">
              <ClockFill size={24} />
              <p className="stat-label">This Week</p>
              <h5 className="stat-value">{stats.thisWeekHours} hrs</h5>
            </div>
          </Col>

          <Col lg={3} md={6} sm={6} className="mb-3">
            <div className="stat-box">
              <Calendar size={24} />
              <p className="stat-label">This Month</p>
              <h5 className="stat-value">{stats.thisMonthHours} hrs</h5>
            </div>
          </Col>

          <Col lg={3} md={6} sm={6} className="mb-3">
            <div className="stat-box">
              <Clock size={24} />
              <p className="stat-label">Attendance</p>
              <h5 className="stat-value">{stats.attendanceRate}%</h5>
            </div>
          </Col>

          <Col lg={3} md={6} sm={6} className="mb-3">
            <div className="stat-box">
              <CheckCircle size={24} />
              <p className="stat-label">On Time</p>
              <h5 className="stat-value">{stats.onTimeRate}%</h5>
            </div>
          </Col>
        </Row>

        {/* Recent Attendance */}
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
                        <strong>
                          {new Date(record.clock_in).toDateString()}
                        </strong>
                      </td>
                      <td>
                        <Badge className="status-badge-present">
                          {record.status}
                        </Badge>
                      </td>
                      <td>
                        {record.clock_in
                          ? formatTime(new Date(record.clock_in))
                          : "--"}
                        {" - "}
                        {record.clock_out
                          ? formatTime(new Date(record.clock_out))
                          : "..."}
                      </td>
                      <td>{record.hours_worked} hrs</td>
                      <td>
                        <Button
                          className="btn-action btn-adjust"
                          onClick={() => handleOpenAdjustModal(record)}
                        >
                          Adjust
                        </Button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </Table>
            </div>
          </Card.Body>
        </Card>
      </Container>

      {/* DTR Adjustment Modal */}
      <Modal
        show={showAdjustModal}
        onHide={handleCloseAdjustModal}
        centered
        size="lg"
      >
        <Modal.Header closeButton>
          <Modal.Title>Request DTR Adjustment</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          {selectedRecord && (
            <>
              {/* Original Times */}
              <div className="mb-4">
                <h6 className="fw-bold mb-3">Original Times</h6>
                <Row>
                  <Col md={6}>
                    <div>
                      <small className="text-muted">Clock In</small>
                      <div className="d-flex align-items-center gap-2">
                        <Clock size={18} className="text-primary" />
                        <strong>
                          {formatTime(new Date(selectedRecord.clock_in))}
                        </strong>
                      </div>
                      <small className="text-muted">
                        {new Date(selectedRecord.clock_in).toLocaleDateString(
                          "en-US",
                          {
                            weekday: "short",
                            month: "short",
                            day: "numeric",
                            year: "numeric",
                          },
                        )}
                      </small>
                    </div>
                  </Col>
                  <Col md={6}>
                    <div>
                      <small className="text-muted">Clock Out</small>
                      <div className="d-flex align-items-center gap-2">
                        <DoorOpen size={18} className="text-primary" />
                        <strong>
                          {selectedRecord.clock_out
                            ? formatTime(new Date(selectedRecord.clock_out))
                            : "Not clocked out"}
                        </strong>
                      </div>
                    </div>
                  </Col>
                </Row>
              </div>

              <hr />

              {/* Adjusted Times */}
              <div className="mb-4">
                <h6 className="fw-bold mb-3">Adjusted Times</h6>

                <Form.Group className="mb-3">
                  <Form.Label className="fw-bold">Adjusted Clock In</Form.Label>
                  <Form.Control
                    type="time"
                    name="adjustedClockIn"
                    value={adjustmentForm.adjustedClockIn}
                    onChange={handleAdjustmentFormChange}
                  />
                </Form.Group>

                <Form.Group className="mb-3">
                  <Form.Label className="fw-bold">
                    Adjusted Clock Out
                  </Form.Label>
                  <Form.Control
                    type="time"
                    name="adjustedClockOut"
                    value={adjustmentForm.adjustedClockOut}
                    onChange={handleAdjustmentFormChange}
                  />
                </Form.Group>

                <small className="text-warning d-block mb-3">
                  <Lightbulb size={16} className="me-1" /> Use the time inputs
                  above to adjust your times
                </small>
              </div>

              {/* Reason for Adjustment */}
              <div className="mb-4">
                <Form.Group>
                  <Form.Label className="fw-bold">
                    Reason for Adjustment
                  </Form.Label>
                  <Form.Control
                    as="textarea"
                    rows={4}
                    name="reason"
                    placeholder="Please explain why you need this adjustment..."
                    value={adjustmentForm.reason}
                    onChange={handleAdjustmentFormChange}
                  />
                </Form.Group>
              </div>

              {/* Info Message */}
              <div className="alert alert-info d-flex gap-2 mb-0">
                <span>💡</span>
                <span>
                  Your adjustment request will be reviewed and approved by your
                  supervisor. You'll be notified once it's processed.
                </span>
              </div>
            </>
          )}
        </Modal.Body>
        <Modal.Footer>
          <Button variant="outline-secondary" onClick={handleCloseAdjustModal}>
            Cancel
          </Button>
          <Button variant="primary" onClick={handleSubmitAdjustment}>
            Submit Request
          </Button>
        </Modal.Footer>
      </Modal>

      {/* Toast Container - Fixed position for better visibility */}
      <ToastContainer
        position="top-end"
        className="p-3"
        style={{ zIndex: 9999 }}
      >
        {toasts.map((toast) => (
          <Toast
            key={toast.id}
            bg={toast.type === "success" ? "success" : "danger"}
            show={toast.show}
            onClose={() => removeToast(toast.id)}
            delay={5000}
            autohide
          >
            <Toast.Body className="text-white">{toast.message}</Toast.Body>
          </Toast>
        ))}
      </ToastContainer>
    </AdminLayout>
  );
};

export default Attendance;
