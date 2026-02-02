import React, { useState } from "react";
import { Container, Row, Col, Form, Button, Alert } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import "bootstrap/dist/css/bootstrap.min.css";
import "./Login.css";
import api from "../../config/axios";
import { Toast, ToastContainer } from "react-bootstrap";
import { useAuth } from "../../context/AuthContext";

const Login = ({ setIsAuth }) => {
  const navigate = useNavigate();
  const { login } = useAuth();
  const [showPassword, setShowPassword] = useState(false);
  const [toast, setShowToast] = useState({
    show: false,
    message: "",
    type: "",
  });

  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    login: "",
    password: "",
  });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    console.log("Login attempt with:", formData);

    //dyanmic login simulation
    try {
      const response = await api.post("/login", formData);
      console.log("Login response:", response.data);

      // Store user data and token using AuthContext
      if (response.data.token && response.data.user) {
        login(response.data.user, response.data.token);
      }

      if (setIsAuth) setIsAuth(true);
      localStorage.setItem("isAuth", "true");

      setShowToast({
        show: true,
        message: "Login successful!",
        type: "success",
      });

      // Navigate after toast shows
      setTimeout(() => {
        navigate("/dashboard");
      }, 1500);
    } catch (error) {
      console.error("Login error:", error);
      const errorMessage = error.response?.data?.message || "Login failed";

      setShowToast({
        show: true,
        message: errorMessage,
        type: "danger",
      });
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
      <ToastContainer position="top-end" className="p-3">
        <Toast
          bg={toast.type}
          show={toast.show}
          onClose={() => setShowToast({ ...toast, show: false })}
          delay={2000}
          autohide
        >
          <Toast.Header closeButton>
            <strong className="me-auto">
              {toast.type === "success" ? "Success" : "Error"}
            </strong>
          </Toast.Header>

          <Toast.Body className="text-white">{toast.message}</Toast.Body>
        </Toast>
      </ToastContainer>
    </Container>
  );
};

export default Login;
