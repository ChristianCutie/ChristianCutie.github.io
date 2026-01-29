import React, { useState } from "react";
import { Container, Row, Col, Form, Button, Card, Toast, ToastContainer } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import "./Register.css";
import { Link } from "react-router";
import axios from "axios";

const Register = () => {

    const api = "http://127.0.0.1:8000/api";

    //fetch user
    const fetchUsers = async () => {
        try {
            const response = await axios.get(`${api}/get-users`);
            console.log("Users fetched:", response.data);
        }   catch (error) {
            console.error("Error fetching users:", error);
        }
    };

  const [formData, setFormData] = useState({
    fullName: "",
    email: "",
    username: "",
    password: "",
    confirmPassword: "",
  });
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();
  const [toast, setShowToast] = useState({
    show: false,
    message: "",
    type: "",
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prevData) => ({
      ...prevData,
      [name]: value,
    }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    // Validate form
    setLoading(true);
    setTimeout(() => {
      const { fullName, email, username, password, confirmPassword } = formData;

      if (!fullName || !email || !username || !password || !confirmPassword) {
        setShowToast({
          show: true,
          message: "Please fill in all fields",
          type: "danger",
        });
        setLoading(false);
        return;
      }

      if (password !== confirmPassword) {
        setShowToast({
          show: true,
          message: "Passwords do not match",
          type: "danger",
        });
        setLoading(false);
        return;
      }

      if (password.length < 6) {
        setShowToast({
          show: true,
          message: "Password must be at least 6 characters",
          type: "danger",
        });
        setLoading(false);
        return;
      }

      // Simulate registration success
      setShowToast({
        show: true,
        message: "Registration successful! Redirecting...",
        type: "success",
      });
      setLoading(false);

      // Navigate after toast shows
      setTimeout(() => {
        navigate("/");
      }, 2000);
    }, 2000);
  };

  return (
    <div className="register-container">
      <Container>
        <Row className="justify-content-center align-items-center min-vh-100">
          <Col md={6} sm={8} xs={12}>
            <Card className="shadow-lg border-0 register-card">
              <Card.Body className="p-5">
                {/* Header */}
                <div className="text-center mb-4">
                  <h2 className="fw-bold text-primary mb-2">
                    Inventory Clinic
                  </h2>
                  <p className="text-muted">Create your account</p>
                </div>

                {/* Register Form */}
                <Form onSubmit={handleSubmit}>
                  {/* Full Name Field */}
                  <Form.Group className="mb-3">
                    <Form.Label className="fw-semibold">Full Name</Form.Label>
                    <Form.Control
                      type="text"
                      placeholder="Enter your full name"
                      name="fullName"
                      value={formData.fullName}
                      onChange={handleChange}
                      className="form-control-lg"
                    />
                  </Form.Group>

                  {/* Email Field */}
                  <Form.Group className="mb-3">
                    <Form.Label className="fw-semibold">Email</Form.Label>
                    <Form.Control
                      type="email"
                      placeholder="Enter your email"
                      name="email"
                      value={formData.email}
                      onChange={handleChange}
                      className="form-control-lg"
                    />
                    <Form.Text className="text-muted">
                      We'll never share your email.
                    </Form.Text>
                  </Form.Group>

                  {/* Username Field */}
                  <Form.Group className="mb-3">
                    <Form.Label className="fw-semibold">Username</Form.Label>
                    <Form.Control
                      type="text"
                      placeholder="Choose a username"
                      name="username"
                      value={formData.username}
                      onChange={handleChange}
                      className="form-control-lg"
                    />
                  </Form.Group>

                  {/* Password Field */}
                  <Form.Group className="mb-3">
                    <Form.Label className="fw-semibold">Password</Form.Label>
                    <Form.Control
                      type="password"
                      placeholder="Create a password"
                      name="password"
                      value={formData.password}
                      onChange={handleChange}
                      className="form-control-lg"
                    />
                  </Form.Group>

                  {/* Confirm Password Field */}
                  <Form.Group className="mb-2">
                    <Form.Label className="fw-semibold">
                      Confirm Password
                    </Form.Label>
                    <Form.Control
                      type="password"
                      placeholder="Confirm your password"
                      name="confirmPassword"
                      value={formData.confirmPassword}
                      onChange={handleChange}
                      className="form-control-lg"
                    />
                  </Form.Group>

                  {/* Submit Button */}
                  <Button
                    variant="primary"
                    size="lg"
                    className="w-100 fw-semibold mb-3 mt-4"
                    type="submit"
                    disabled={loading}
                  >
                    {loading ? "Creating account..." : "Sign Up"}
                  </Button>
                </Form>

                {/* Divider */}
                <div className="d-flex align-items-center my-4">
                  <hr className="flex-grow-1" />
                  <span className="mx-2 text-muted small">OR</span>
                  <hr className="flex-grow-1" />
                </div>

                {/* Login Link */}
                <p className="text-center text-muted">
                  Already have an account?{" "}
                  <Link to="/" className="text-primary text-decoration-none fw-semibold">
                    Sign in here
                  </Link>
                </p>
              </Card.Body>
            </Card>
            {/* Footer */}
            <div className="text-center mt-4 text-muted small">
              <p>© 2026 Inventory Clinic. All rights reserved.</p>
            </div>
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

            <Toast.Body className="text-white">
              {toast.message}
            </Toast.Body>
          </Toast>
        </ToastContainer>
      </Container>
    </div>
  );
};

export default Register;
