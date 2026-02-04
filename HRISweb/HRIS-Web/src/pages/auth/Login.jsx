import React, { useState } from "react";
import { Container, Row, Col, Form, Button, Alert } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import "bootstrap/dist/css/bootstrap.min.css";
import "./Login.css";
import api from "../../config/axios";
import { Toast, ToastContainer } from "react-bootstrap";
import { useAuth } from "../../context/AuthContext";
import "./../../assets/style/global.css";

const Login = ({ setIsAuth }) => {
  const navigate = useNavigate();
  const { login } = useAuth();
  const [showPassword, setShowPassword] = useState(false);
  const [toasts, setToasts] = useState([]);
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    login: "",
    password: "",
  });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  /* =======================
     TOAST FUNCTIONS
  ======================== */
  const showToast = (message, type = "success", redirectTo = null) => {
    const id = Date.now();
    const newToast = {
      id,
      message,
      type,
      show: true,
      redirectTo,
    };
    
    setToasts((prevToasts) => [...prevToasts, newToast]);

    // Auto remove toast after 3 seconds, redirect if needed
    setTimeout(() => {
      removeToast(id);
      if (redirectTo) {
        setTimeout(() => navigate(redirectTo), 100); // Small delay for smooth transition
      }
    }, 3000);
  };

  const removeToast = (id) => {
    setToasts((prevToasts) => 
      prevToasts.map(toast => 
        toast.id === id ? { ...toast, show: false } : toast
      )
    );
    
    // Remove from array after fade out animation
    setTimeout(() => {
      setToasts((prevToasts) => prevToasts.filter(toast => toast.id !== id));
    }, 300);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    console.log("Login attempt with:", formData);

    try {
      const response = await api.post("/login", formData);
      console.log("Login response:", response.data);

      // Store user data and token using AuthContext
      if (response.data.token && response.data.user) {
        login(response.data.user, response.data.token);
      }

      if (setIsAuth) setIsAuth(true);
      localStorage.setItem("isAuth", "true");

      // Show success toast with dashboard redirect
      showToast("Login successful! Redirecting to dashboard...", "success", "/dashboard");

    } catch (error) {
      console.error("Login error:", error);
      const errorMessage = error.response?.data?.message || "Login failed";

      // Show error toast (no redirect)
      showToast(errorMessage, "danger");
    } finally {
      setLoading(false);
    }
  };

  return (
    <Container fluid className="login-container">
      <Row className="h-100 align-items-center justify-content-center">
        <Col md={5} className="login-card p-5">
          <div className="text-center mb-4">
            <h1 className="welcome-heading">Welcome Back</h1>
            <p className="login-subtitle">Login to your HRIS account</p>
          </div>

          <Form onSubmit={handleSubmit}>
            {/* Email Field */}
            <Form.Group className="mb-3">
              <Form.Control
                type="email"
                placeholder="Enter your email"
                value={formData.login}
                name="login"
                onChange={handleChange}
                className="form-control-lg login-input"
                required
              />
            </Form.Group>

            {/* Password Field */}
            <Form.Group className="mb-3 position-relative">
              <Form.Control
                type={showPassword ? "text" : "password"}
                placeholder="Enter your password"
                value={formData.password}
                name="password"
                onChange={handleChange}
                className="form-control-lg login-input"
                required
              />
              <button
                type="button"
                className="password-toggle"
                onClick={() => setShowPassword(!showPassword)}
              >
                <i
                  className={`bi ${showPassword ? "bi-eye-slash" : "bi-eye"}`}
                ></i>
              </button>
            </Form.Group>

            {/* Warning Message */}
            <Alert variant="light" className="password-warning mb-4">
              <i className="bi bi-exclamation-circle-fill warning-icon"></i>
              <span className="ms-2">
                Make sure to enter your password exactly as set, including any
                spaces
              </span>
            </Alert>

            {/* Sign In Button */}
            <Button
              variant="primary"
              size="lg"
              type="submit"
              className="w-100 signin-btn mb-4"
              disabled={loading}
            >
              {loading ? "Signing in..." : "Sign In"}
            </Button>
          </Form>

          {/* Platform Info */}
          <p className="text-center platform-text">Platform: Web</p>
        </Col>
      </Row>

      {/* Toast Container */}
      <ToastContainer position="top-end" className="p-3" style={{ zIndex: 9999 }}>
        {toasts.map((toast) => (
          <Toast
            key={toast.id}
            bg={toast.type === "success" ? "success" : "danger"}
            show={toast.show}
            onClose={() => removeToast(toast.id)}
            delay={3000}
            autohide
          >
            <Toast.Header closeButton>
              <strong className="me-auto">
                {toast.type === "success" ? "Success" : "Error"}
              </strong>
            </Toast.Header>
            <Toast.Body className="text-white">
              {toast.message}
            </Toast.Body>
          </Toast>
        ))}
      </ToastContainer>
    </Container>
  );
};

export default Login;