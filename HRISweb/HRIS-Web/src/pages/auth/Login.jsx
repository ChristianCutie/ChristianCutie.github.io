import React, { useState } from "react";
import { Container, Row, Col, Form, Button, Alert } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import "bootstrap/dist/css/bootstrap.min.css";
import "./Login.css";

const Login = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  
  const navigate = useNavigate();

  const handleSubmit = (e) => {
    e.preventDefault();
    console.log("Login attempt with:", { email, password });

    //static login simulation
    if (email === "test@gmail.com" && password === "1234") {
      navigate("/dashboard");
    } else {
      alert("Invalid email or password");
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
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="form-control-lg login-input"
                required
              />
            </Form.Group>

            {/* Password Field */}
            <Form.Group className="mb-3 position-relative">
              <Form.Control
                type={showPassword ? "text" : "password"}
                placeholder="Enter your password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
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
            >
              Sign In
            </Button>
          </Form>

          {/* Platform Info */}
          <p className="text-center platform-text">Platform: Web</p>
        </Col>
      </Row>
    </Container>
  );
};

export default Login;
