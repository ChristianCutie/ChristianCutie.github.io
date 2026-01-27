import React, { useState } from "react";
import { Container, Row, Col, Form, Button, Card } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import "./Login.css";
import Alert from "../../components/common/Alert.jsx";

const Login = ({ setIsAuth }) => {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  const [alert, setAlert] = useState({
    show: false,
    message: "",
    type: "",
  });

  const handleSubmit = (e) => {
    e.preventDefault();

    // Simulate login process
    setLoading(true);
    setTimeout(() => {
      if (!username || !password) {
        setAlert({
          show: true,
          message: "Please fill in all fields",
          type: "danger",
        });
        setLoading(false);
        return;
      }

      if (username === "admin" && password === "1234") {
        if (setIsAuth) setIsAuth(true);
        localStorage.setItem("isAuth", "true");
        setAlert({
          show: true,
          message: "Login successful!",
          type: "success",
        });
        setLoading(false);
        navigate("/admin/dashboard");
      } else {
        setAlert({
          show: true,
          message: "Invalid username or password",
          type: "danger",
        });
        setLoading(false);
      }
    }, 500);
  };

  return (
    <div className="login-container">
      <Container>
        <Row className="justify-content-center align-items-center min-vh-100">
          <Col md={5} sm={8} xs={12}>
            <Card className="shadow-lg border-0 login-card">
              <Card.Body className="p-5">
                {/* Header */}
                <div className="text-center mb-4">
                  <h2 className="fw-bold text-primary mb-2">
                    Inventory Clinic
                  </h2>
                  <p className="text-muted">Sign in to your account</p>
                </div>

                {/* Login Form */}
                <Form onSubmit={handleSubmit}>
                  {/* Email Field */}
                  <Form.Group className="mb-3">
                    <Form.Label className="fw-semibold">
                      Username
                    </Form.Label>
                    <Form.Control
                      type="text"
                      placeholder="Enter your username"
                      value={username}
                      onChange={(e) => setUsername(e.target.value)}
                      className="form-control-lg"
                    />
                    <Form.Text className="text-muted">
                      We'll never share your email.
                    </Form.Text>
                  </Form.Group>

                  {/* Password Field */}
                  <Form.Group className="mb-2">
                    <Form.Label className="fw-semibold">Password</Form.Label>
                    <Form.Control
                      type="password"
                      placeholder="Enter your password"
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      className="form-control-lg"
                    />
                  </Form.Group>

                  {/* Forgot Password Link */}
                  <div className="text-end mb-4">
                    <a
                      href="#forgot-password"
                      className="text-primary text-decoration-none small"
                    >
                      Forgot password?
                    </a>
                  </div>

                  {/* Submit Button */}
                  <Button
                    variant="primary"
                    size="lg"
                    className="w-100 fw-semibold mb-3"
                    type="submit"
                    disabled={loading}
                  >
                    {loading ? "Signing in..." : "Sign In"}
                  </Button>
                </Form>

                {/* Divider */}
                <div className="d-flex align-items-center my-4">
                  <hr className="flex-grow-1" />
                  <span className="mx-2 text-muted small">OR</span>
                  <hr className="flex-grow-1" />
                </div>

                {/* Sign Up Link */}
                <p className="text-center text-muted">
                  Don't have an account?{" "}
                  <a
                    href="#signup"
                    className="text-primary text-decoration-none fw-semibold"
                  >
                    Sign up here
                  </a>
                </p>
              </Card.Body>
            </Card>
            {/* Footer */}
            <div className="text-center mt-4 text-muted small">
              <p>© 2026 Inventory Clinic. All rights reserved.</p>
            </div>
          </Col>
        </Row>
        <Alert
          show={alert.show}
          message={alert.message}
          type={alert.type}
          onClose={() => setAlert({ ...alert, show: false })}
        />
      </Container>
    </div>
  );
};

export default Login;
