import React, { useState, useEffect, use } from "react";
import PayslipPDF from "../../components/payslip/PayslipPDF";
import jsPDF from "jspdf";
import html2canvas from "html2canvas";
import { useRef } from "react";

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

const Payslip = ({ setIsAuth }) => {
  const [searchTerm, setSearchTerm] = useState("");
  const [compactView, setCompactView] = useState(false);
  const [show, setShow] = useState(false);
  const handleClose = () => setShow(false);
  const handleShow = () => setShow(true);

  // Ref for PDF generation
  const pdfRef = useRef();
  const hasFetched = useRef(false);

  // Selected payslip detail
  const [selectedPayslip, setSelectedPayslip] = useState(null);

  //Deductions SSS, Philhealth, Pagibig
  const [allDeductions, setDeductions] = useState([]);

  //Remarks
  const [remarks, setRemarks] = useState([]);

  //period Count
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  //All Period Count
  const [periodCount, setPeriodCount] = useState([]);

  //`Payslips Data
  const [payslips, setPayslips] = useState([]);

  //Total Allowances
  const [totalAllowances, setTotalAllowances] = useState(0);
  const [allowances, setAllowances] = useState([]);

  //Total Gross, Deductions, Net Pay
  const [totalGross, setTotalGross] = useState(0);
  const [totalDeductions, setTotalDeductions] = useState(0);
  const [totalNetPay, setTotalNetPay] = useState(0);

  // Format number to Philippine Peso
  const formatPeso = (value) =>
    new Intl.NumberFormat("en-PH", {
      style: "currency",
      currency: "PHP",
      minimumFractionDigits: 2,
    }).format(value);

  // Download PDF
  const downloadPDF = async () => {
    const canvas = await html2canvas(pdfRef.current, { scale: 2 });
    const imgData = canvas.toDataURL("image/png");

    const pdf = new jsPDF("p", "mm", "a4");
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

    pdf.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
    pdf.save(`Payslip-${selectedPayslip.period}.pdf`);
  };

  // Fetch payslip by ID
  const fetchPayslipById = async (record_id) => {
    try {
      const res = await api.get(`/my-payslip/${record_id}`);
      const detail = res.data?.payslip;
      setSelectedPayslip(detail);
    } catch (error) {
      console.error(
        "Error fetching payslip detail:",
        error.response?.data || error.message,
      );
    }
  };
  useEffect(() => {

    if (hasFetched.current) return;
  hasFetched.current = true;
    //fetch selected payslip details

    /**
     * Fetches payslip records from the API and updates the state.
     * @returns {Promise<void>}
     */
    const fetchPayslips = async () => {
      try {
        const res = await api.get("/my-payroll-records");

        const records = res.data?.data || [];
        setPayslips(records);

        // Totals
        const totalGross = records.reduce(
          (sum, p) => sum + Number(String(p.gross_pay).replace(/,/g, "")),
          0,
        );
        const totalDeductions = records.reduce(
          (sum, p) =>
            sum + Number(String(p.total_deductions).replace(/,/g, "")),
          0,
        );
        const totalNetPay = records.reduce(
          (sum, p) => sum + Number(String(p.net_pay).replace(/,/g, "")),
          0,
        );

        // Fetch Period Count
        const periodCount = records.map((record) => record.period || "");
        setPeriodCount(periodCount);

        // Remarks
        const allRemarks = records.map((record) => record.remarks || "");
        setRemarks(allRemarks);

        // Flatten deductions
        const allDeductions = records.flatMap(
          (record) => record.deductions || [],
        );
        setDeductions(allDeductions);

        // Flatten allowances
        const allAllowances = records.flatMap(
          (record) => record.allowances || [],
        );
        setAllowances(allAllowances);

        const totalAllowances = allAllowances.reduce(
          (sum, a) =>
            sum + Number(String(a.allowance_amount).replace(/,/g, "")),
          0,
        );

        // Set totals
        setTotalGross(totalGross);
        setTotalDeductions(totalDeductions);
        setTotalNetPay(totalNetPay);
        setTotalAllowances(totalAllowances);

        // Pagination
        const pagination = res.data?.pagination;
        if (pagination) {
          setCurrentPage(pagination.current_page);
          setLastPage(pagination.last_page);
        }
      } catch (error) {
        console.error(
          "Error fetching payslips:",
          error.response?.data || error.message,
        );
      }
    };

    fetchPayslips();
  }, []);

  // Sample payslip data
  const payslipData = {
    period: "January 2025",
    periodCount: "1 of 1",
    dateRange: "January 1-15, 2026",
    generatedDate: "January 21, 2026 07:33 AM",
    grossPay: totalGross,
    totalDeductions: totalDeductions,
    netPay: totalNetPay,
    attendanceNote: remarks,
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
              <p className="summary-period">
                Page: {currentPage} of {lastPage}
              </p>
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
                    <h5 className="summary-value">{formatPeso(totalGross)}</h5>
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
                      {formatPeso(totalDeductions)}
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
                      {formatPeso(totalNetPay)}
                    </h5>
                  </Card.Body>
                </Card>
              </Col>
            </Row>
          </div>
          {/* End of Payroll Summary */}
          {/* Attendance Note */}
          <div className="payroll-summary-2 mt-4">
            <div className="summary-header">
              <h5 className="summary-title">
                {periodCount.map((period, index) => (
                  <span key={index}>{period}</span>
                ))}
              </h5>
            </div>
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
                          {formatPeso(totalGross)}
                        </h6>
                      </div>
                    </Col>
                    <Col md={4}>
                      <div className="detail-item">
                        <p className="detail-label">Deductions</p>
                        <h6 className="detail-value detail-value-danger">
                          {formatPeso(totalDeductions)}
                        </h6>
                      </div>
                    </Col>
                    <Col md={4}>
                      <div className="detail-item">
                        <p className="detail-label">Net Pay</p>
                        <h6 className="detail-value detail-value-success">
                          {formatPeso(totalNetPay)}
                        </h6>
                      </div>
                    </Col>
                  </Row>

                  {/* Allowances Section */}
                  <div className="breakdown-section mb-4">
                    <h6 className="breakdown-title">Allowances</h6>
                    {allowances.map((a, index) => (
                      <div key={index} className="breakdown-item">
                        <span className="breakdown-name">
                          {a.allowance_type}
                        </span>
                        <span className="breakdown-amount breakdown-amount-positive">
                          +{" "}
                          {formatPeso(
                            Number(
                              String(a.allowance_amount).replace(/,/g, ""),
                            ),
                          )}
                        </span>
                      </div>
                    ))}
                  </div>

                  {/* Deductions Section */}
                  <div className="breakdown-section">
                    <h6 className="breakdown-title">Deductions</h6>
                    {allDeductions.map((d, index) => (
                      <div key={index} className="breakdown-item">
                        <span className="breakdown-name">
                          {d.deduction_type}
                        </span>
                        <span className="breakdown-amount breakdown-amount-negative">
                          -{" "}
                          {formatPeso(
                            Number(
                              String(d.deduction_amount).replace(/,/g, ""),
                            ),
                          )}
                        </span>
                      </div>
                    ))}
                  </div>
                </Card.Body>
              </Card>
            )}

            {/* View Full Details Button */}
            {payslips.map((payslip) => (
              <div
                key={payslip.record_id}
                className="view-details-button-container"
              >
                <Button
                  onClick={() => {
                    fetchPayslipById(payslip.record_id);
                    handleShow();
                  }}
                  className="btn-view-details"
                >
                  <FileEarmarkText size={18} className="me-2" />
                  View Full Details
                </Button>
              </div>
            ))}
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
          {selectedPayslip && (
            <Offcanvas.Body className="payslip-offcanvas-body p-4">
              {/* Date Range */}
              <div className="payslip-detail-section">
                <div className="detail-date-info">
                  <p className="detail-date-label">
                    <CalendarDate size={16} className="me-2" />
                    {selectedPayslip.period}
                  </p>
                  <p className="detail-generated">
                    Generated: {selectedPayslip.generated_at}
                  </p>
                </div>
              </div>

              {/* Remarks Section */}

              <div className="payslip-detail-section">
                <h6 className="detail-section-title">Remarks</h6>
                <div className="remarks-box">
                  <p className="remarks-text">{selectedPayslip.remarks}</p>
                </div>
              </div>
              {/* Summary Boxes */}
              <Row className="mb-4">
                <Col xs={4} className="mb-2">
                  <div className="summary-detail-box summary-detail-blue">
                    <p className="summary-detail-label">Total Gross Pay</p>
                    <h6 className="summary-detail-value">
                      {formatPeso(totalGross)}
                    </h6>
                  </div>
                </Col>
                <Col xs={4} className="mb-2">
                  <div className="summary-detail-box summary-detail-yellow">
                    <p className="summary-detail-label">Total Deductions</p>
                    <h6 className="summary-detail-value">
                      {formatPeso(totalDeductions)}
                    </h6>
                  </div>
                </Col>
                <Col xs={4} className="mb-2">
                  <div className="summary-detail-box summary-detail-green">
                    <p className="summary-detail-label">Net Pay</p>
                    <h6 className="summary-detail-value">
                      {formatPeso(totalNetPay)}
                    </h6>
                  </div>
                </Col>
              </Row>

              {/* Allowances Section */}
              <div className="payslip-detail-section">
                <h6 className="detail-section-title">Allowances</h6>
                {allowances.map((allowance, idx) => (
                  <div key={idx} className="allowance-deduction-item">
                    <span className="item-name">
                      {allowance.allowance_type}
                    </span>
                    <span className="item-amount positive">
                      +
                      {formatPeso(
                        Number(
                          String(allowance.allowance_amount).replace(/,/g, ""),
                        ),
                      )}
                    </span>
                  </div>
                ))}
              </div>

              {/* Deductions Section */}
              <div className="payslip-detail-section">
                <h6 className="detail-section-title">Deductions</h6>
                {allDeductions.map((deduction, idx) => (
                  <div key={idx} className="allowance-deduction-item">
                    <span className="item-name">
                      {deduction.deduction_type}
                    </span>
                    <span className="item-amount negative">
                      -
                      {formatPeso(
                        Number(
                          String(deduction.deduction_amount).replace(/,/g, ""),
                        ),
                      )}
                    </span>
                  </div>
                ))}
              </div>

              {/* Totals Summary */}
              <div className="payslip-detail-section">
                <div className="allowance-deduction-item">
                  <span className="item-name">
                    <strong>Total Gross Pay</strong>
                  </span>
                  <span className="item-amount">
                    <strong>{formatPeso(totalGross)}</strong>
                  </span>
                </div>
                <div className="allowance-deduction-item">
                  <span className="item-name">
                    <strong>Total Deductions</strong>
                  </span>
                  <span className="item-amount negative">
                    <strong>{formatPeso(totalDeductions)}</strong>
                  </span>
                </div>
              </div>

              {/* Download PDF Button */}
              <div className="d-flex justify-content-end w-100">
                <Button className="btn-download-pdf w-100 mt-4" onClick={downloadPDF}>
                  <Download size={18} className="me-2" />
                  Download as PDF
                </Button>
              </div>
            </Offcanvas.Body>
          )}
        </Offcanvas>
        {/* Hidden but renderable */}
        <div style={{ position: "absolute", left: "-9999px", top: 0 }}>
          <PayslipPDF
            ref={pdfRef}
            payslip={selectedPayslip}
            formatPeso={formatPeso}
          />
        </div>
      </Container>
    </AdminLayout>
  );
};

export default Payslip;
