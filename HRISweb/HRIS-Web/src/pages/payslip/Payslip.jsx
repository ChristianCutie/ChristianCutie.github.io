import React, { useState } from "react";
import {
  Container,
  Row,
  Col,
  Card,
  Button,
  InputGroup,
  Form,
} from "react-bootstrap";
import AdminLayout from "../../components/layout/Adminlayout";
import {
  Search,
  GraphUpArrow,
  FileText,
  CurrencyDollar,
  FileEarmarkText,
  CalendarDate,
  Download,
} from "react-bootstrap-icons";
import "./Payslip.css";
import Offcanvas from "react-bootstrap/Offcanvas";
import api from "../../config/axios";

const Payslip = ({ setIsAuth, ...props }) => {
  const [searchTerm, setSearchTerm] = useState("");
  const [compactView, setCompactView] = useState(false);
  const [show, setShow] = useState(false);
  const handleClose = () => setShow(false);
  const handleShow = () => setShow(true);


  

  // Sample payslip data
  const payslipData = {
    period: "January 2025",
    periodCount: "1 of 1",
    dateRange: "January 1-15, 2026",
    generatedDate: "January 21, 2026 07:33 AM",
    grossPay: 6516.0,
    totalDeductions: 425.0,
    netPay: 6091.0,
    attendanceNote: "Jan. 1, 2.9 absent",
    allowances: [
      { name: "Clothing Allowance", amount: 750.0 },
      { name: "Transportation Allowance", amount: 750.0 },
    ],
    deductions: [
      { name: "Pagibig", amount: 100.0 },
      { name: "Philhealth", amount: 125.0 },
      { name: "SSS", amount: 200.0 },
    ],
  };

  return (
    <AdminLayout setIsAuth={setIsAuth}>
      <Container fluid className="payslip-container">
        {/* Page Header */}
        <div className="payslip-header">
          <h2 className="payslip-title fw-bold">Payslip</h2>
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
                        <span className="breakdown-name">{allowance.name}</span>
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
                        <span className="breakdown-name">{deduction.name}</span>
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
            <div className="view-details-button-container">
              <Button
                onClick={handleShow}
                className="btn-view-details"
              >
                <FileEarmarkText size={18} className="me-2" />
                View Full Details
              </Button>
            </div>
          </div>
        </div>
        <Offcanvas
          show={show}
          onHide={handleClose}
          placement="bottom"
          className="payslip-offcanvas h-75"
        >
          <Offcanvas.Header closeButton className="payslip-offcanvas-header">
            <Offcanvas.Title className="payslip-offcanvas-title">
              <FileEarmarkText size={20} className="me-2" />
              Payslip Details
            </Offcanvas.Title>
          </Offcanvas.Header>
          <Offcanvas.Body className="payslip-offcanvas-body p-4">
            {/* Date Range */}
            <div className="payslip-detail-section">
              <div className="detail-date-info">
                <p className="detail-date-label">
                  <CalendarDate size={16} className="me-2" />
                  {payslipData.dateRange}
                </p>
                <p className="detail-generated">
                  Generated: {payslipData.generatedDate}
                </p>
              </div>
            </div>

            {/* Remarks Section */}
            <div className="payslip-detail-section">
              <h6 className="detail-section-title">Remarks</h6>
              <div className="remarks-box">
                <p className="remarks-text">{payslipData.attendanceNote}</p>
              </div>
            </div>

            {/* Summary Boxes */}
            <Row className="mb-4">
              <Col xs={4} className="mb-2">
                <div className="summary-detail-box summary-detail-blue">
                  <p className="summary-detail-label">Gross Pay</p>
                  <h6 className="summary-detail-value">
                    ₱{payslipData.grossPay.toFixed(2)}
                  </h6>
                </div>
              </Col>
              <Col xs={4} className="mb-2">
                <div className="summary-detail-box summary-detail-yellow">
                  <p className="summary-detail-label">Total Deductions</p>
                  <h6 className="summary-detail-value">
                    -₱{payslipData.totalDeductions.toFixed(2)}
                  </h6>
                </div>
              </Col>
              <Col xs={4} className="mb-2">
                <div className="summary-detail-box summary-detail-green">
                  <p className="summary-detail-label">Net Pay</p>
                  <h6 className="summary-detail-value">
                    ₱{payslipData.netPay.toFixed(2)}
                  </h6>
                </div>
              </Col>
            </Row>

            {/* Allowances Section */}
            <div className="payslip-detail-section">
              <h6 className="detail-section-title">Allowances</h6>
              {payslipData.allowances.map((allowance, idx) => (
                <div key={idx} className="allowance-deduction-item">
                  <span className="item-name">{allowance.name}</span>
                  <span className="item-amount positive">
                    +₱{allowance.amount.toFixed(2)}
                  </span>
                </div>
              ))}
            </div>

            {/* Deductions Section */}
            <div className="payslip-detail-section">
              <h6 className="detail-section-title">Deductions</h6>
              {payslipData.deductions.map((deduction, idx) => (
                <div key={idx} className="allowance-deduction-item">
                  <span className="item-name">{deduction.name}</span>
                  <span className="item-amount negative">
                    -₱{deduction.amount.toFixed(2)}
                  </span>
                </div>
              ))}
            </div>

            {/* Download PDF Button */}
            <div className="d-flex justify-content-end w-100">
              <Button className="btn-download-pdf w-100 mt-4">
                <Download size={18} className="me-2" />
                Download as PDF
              </Button>
            </div>
          </Offcanvas.Body>
        </Offcanvas>
      </Container>
    </AdminLayout>
  );
};

export default Payslip;
