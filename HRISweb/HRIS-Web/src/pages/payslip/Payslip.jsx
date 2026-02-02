import React, { useState } from "react";
import { Container, Row, Col, Card, Button, InputGroup, Form } from "react-bootstrap";
import AdminLayout from "../../components/layout/Adminlayout";
import {
  Search,
  GraphUpArrow,
  FileText,
  CurrencyDollar,
  FileEarmarkText,
} from "react-bootstrap-icons";
import "./Payslip.css";

const Payslip = ({ setIsAuth }) => {
  const [searchTerm, setSearchTerm] = useState("");
  const [compactView, setCompactView] = useState(false);

  // Sample payslip data
  const payslipData = {
    period: "January 2025",
    periodCount: "1 of 1",
    grossPay: 6516.00,
    totalDeductions: 425.00,
    netPay: 6091.00,
    attendanceNote: "Jan. 1, 29 absent",
    allowances: [
      { name: "Clothing Allowance", amount: 750.00 },
      { name: "Transportation Allowance", amount: 750.00 },
    ],
    deductions: [
      { name: "Pagibig", amount: 100.00 },
      { name: "Philhealth", amount: 125.00 },
      { name: "SSS", amount: 200.00 },
    ],
  };

  return (
    <AdminLayout setIsAuth={setIsAuth}>
      <Container fluid className="payslip-container">
        {/* Page Header */}
        <div className="payslip-header">
          <h2 className="payslip-title">Payslip</h2>
        </div>

        {/* My Payslips Section */}
        <div className="payslip-section">
          <div className="payslip-header-section">
            <div>
              <h4 className="section-title">My Payslips</h4>
              <p className="section-subtitle">
                Access your salary slips and payment records
              </p>
            </div>
            <Button
              className="btn-compact-view"
              variant="outline-secondary"
              onClick={() => setCompactView(!compactView)}
            >
              {compactView ? "Detailed View" : "Compact View"}
            </Button>
          </div>

          {/* Search Bar */}
          <div className="search-section mb-4">
            <InputGroup className="payslip-search">
              <InputGroup.Text className="search-icon">
                <Search size={18} />
              </InputGroup.Text>
              <Form.Control
                placeholder="Search payslips or remarks..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="payslip-search-input"
              />
            </InputGroup>
          </div>

          {/* Payroll Summary */}
          <div className="payroll-summary">
            <div className="summary-header">
              <h5 className="summary-title">Payroll Summary</h5>
              <p className="summary-period">{payslipData.periodCount} Period</p>
            </div>

            {/* Summary Cards */}
            <Row className="mb-4">
              <Col md={4} className="mb-3">
                <Card className="summary-card">
                  <Card.Body className="text-center">
                    <div className="summary-icon summary-icon-primary">
                      <GraphUpArrow size={28} />
                    </div>
                    <p className="summary-label">Total Gross</p>
                    <h5 className="summary-value">
                      ₱{payslipData.grossPay.toFixed(2)}
                    </h5>
                  </Card.Body>
                </Card>
              </Col>
              <Col md={4} className="mb-3">
                <Card className="summary-card">
                  <Card.Body className="text-center">
                    <div className="summary-icon summary-icon-warning">
                      <FileText size={28} />
                    </div>
                    <p className="summary-label">Total Deductions</p>
                    <h5 className="summary-value summary-value-danger">
                      ₱{payslipData.totalDeductions.toFixed(2)}
                    </h5>
                  </Card.Body>
                </Card>
              </Col>
              <Col md={4} className="mb-3">
                <Card className="summary-card">
                  <Card.Body className="text-center">
                    <div className="summary-icon summary-icon-success">
                      <CurrencyDollar size={28} />
                    </div>
                    <p className="summary-label">Total Net Pay</p>
                    <h5 className="summary-value summary-value-success">
                      ₱{payslipData.netPay.toFixed(2)}
                    </h5>
                  </Card.Body>
                </Card>
              </Col>
            </Row>

            {/* Attendance Note */}
            <div className="attendance-note mb-4">
              <p className="attendance-text">{payslipData.attendanceNote}</p>
            </div>

            {/* Payslip Details */}
            {!compactView && (
              <Card className="payslip-details-card mb-4">
                <Card.Body>
                  <Row className="mb-4">
                    <Col md={4}>
                      <div className="detail-item">
                        <p className="detail-label">Gross Pay</p>
                        <h6 className="detail-value">
                          ₱{payslipData.grossPay.toFixed(2)}
                        </h6>
                      </div>
                    </Col>
                    <Col md={4}>
                      <div className="detail-item">
                        <p className="detail-label">Deductions</p>
                        <h6 className="detail-value detail-value-danger">
                          ₱{payslipData.totalDeductions.toFixed(2)}
                        </h6>
                      </div>
                    </Col>
                    <Col md={4}>
                      <div className="detail-item">
                        <p className="detail-label">Net Pay</p>
                        <h6 className="detail-value detail-value-success">
                          ₱{payslipData.netPay.toFixed(2)}
                        </h6>
                      </div>
                    </Col>
                  </Row>

                  {/* Allowances Section */}
                  <div className="breakdown-section mb-4">
                    <h6 className="breakdown-title">Allowances</h6>
                    {payslipData.allowances.map((allowance, idx) => (
                      <div key={idx} className="breakdown-item">
                        <span className="breakdown-name">
                          {allowance.name}
                        </span>
                        <span className="breakdown-amount breakdown-amount-positive">
                          +₱{allowance.amount.toFixed(2)}
                        </span>
                      </div>
                    ))}
                  </div>

                  {/* Deductions Section */}
                  <div className="breakdown-section">
                    <h6 className="breakdown-title">Deductions</h6>
                    {payslipData.deductions.map((deduction, idx) => (
                      <div key={idx} className="breakdown-item">
                        <span className="breakdown-name">
                          {deduction.name}
                        </span>
                        <span className="breakdown-amount breakdown-amount-negative">
                          -₱{deduction.amount.toFixed(2)}
                        </span>
                      </div>
                    ))}
                  </div>
                </Card.Body>
              </Card>
            )}

            {/* View Full Details Button */}
            <Button className="btn-view-details w-100">
              <FileEarmarkText size={18} className="me-2" />
              View Full Details
            </Button>
          </div>
        </div>
      </Container>
    </AdminLayout>
  );
};

export default Payslip;
