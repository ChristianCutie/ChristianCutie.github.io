<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEDREC - Improved Appointments</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #334155;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }
        
        .header h1 {
            color: #1e293b;
            font-size: 28px;
            margin-bottom: 8px;
        }
        
        .header p {
            color: #64748b;
            font-size: 16px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }
        
        .stat-card.total { border-left-color: #3b82f6; }
        .stat-card.cancelled { border-left-color: #ef4444; }
        .stat-card.pending { border-left-color: #f59e0b; }
        .stat-card.completed { border-left-color: #10b981; }
        .stat-card.upcoming { border-left-color: #8b5cf6; }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .stat-label {
            color: #64748b;
            font-size: 14px;
        }
        
        .controls {
            display: flex;
            justify-content: between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        
        .view-toggle {
            display: flex;
            background: white;
            border-radius: 8px;
            padding: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .view-btn {
            padding: 8px 16px;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .view-btn.active {
            background: #3b82f6;
            color: white;
        }
        
        .filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 8px 16px;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .filter-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .search-bar {
            flex: 1;
            max-width: 400px;
            position: relative;
        }
        
        .search-bar input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }
        
        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        
        .appointment-section {
            margin-bottom: 32px;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
        }
        
        .section-count {
            margin-left: 12px;
            background: #f1f5f9;
            color: #64748b;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
        }
        
        .appointment-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 16px;
            overflow: hidden;
            transition: all 0.2s;
            border-left: 4px solid;
        }
        
        .appointment-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .appointment-card.urgent { border-left-color: #ef4444; }
        .appointment-card.upcoming { border-left-color: #3b82f6; }
        .appointment-card.pending { border-left-color: #f59e0b; }
        .appointment-card.completed { border-left-color: #10b981; }
        
        .card-content {
            padding: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        
        .doctor-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .doctor-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
        }
        
        .doctor-details h3 {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 4px;
        }
        
        .doctor-specialty {
            color: #64748b;
            font-size: 14px;
        }
        
        .appointment-reason {
            color: #3b82f6;
            font-size: 14px;
            font-weight: 500;
            margin-top: 4px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-confirmed {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .card-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-icon {
            width: 20px;
            height: 20px;
            color: #64748b;
        }
        
        .info-text {
            color: #1e293b;
            font-size: 14px;
        }
        
        .time-indicator {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .time-indicator.soon {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .card-actions {
            display: flex;
            gap: 8px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }
        
        .action-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid;
            flex: 1;
            text-align: center;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        .btn-secondary {
            background: white;
            color: #64748b;
            border-color: #e2e8f0;
        }
        
        .btn-danger {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }
        
        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .new-appointment-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 50%;
            width: 56px;
            height: 56px;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            transition: all 0.2s;
        }
        
        .new-appointment-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.5);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }
            
            .controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-bar {
                max-width: none;
            }
            
            .card-body {
                grid-template-columns: 1fr;
            }
            
            .card-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>My Appointments</h1>
            <p>Manage your medical appointments and health visits</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-number">2</div>
                <div class="stat-label">Total Appointments</div>
            </div>
            <div class="stat-card upcoming">
                <div class="stat-number">2</div>
                <div class="stat-label">Upcoming</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number">1</div>
                <div class="stat-label">Pending Confirmation</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-number">0</div>
                <div class="stat-label">Completed This Month</div>
            </div>
            <div class="stat-card cancelled">
                <div class="stat-number">0</div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>
        
        <div class="controls">
            <div class="view-toggle">
                <button class="view-btn active">📋 List View</button>
                <button class="view-btn">📅 Calendar View</button>
                <button class="view-btn">📊 Timeline View</button>
            </div>
            
            <div class="filters">
                <button class="filter-btn active">All</button>
                <button class="filter-btn">This Week</button>
                <button class="filter-btn">Next Month</button>
                <button class="filter-btn">Cardiology</button>
                <button class="filter-btn">Pending</button>
            </div>
            
            <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" placeholder="Search appointments, doctors, or conditions...">
            </div>
        </div>
        
        <div class="appointment-section">
            <div class="section-header">
                <h2 class="section-title">This Week</h2>
                <span class="section-count">2 appointments</span>
            </div>
            
            <div class="appointment-card upcoming">
                <div class="card-content">
                    <div class="card-header">
                        <div class="doctor-info">
                            <div class="doctor-avatar">KE</div>
                            <div class="doctor-details">
                                <h3>Dr. Kaloy E Enriquez</h3>
                                <div class="doctor-specialty">Cardiology • Heart Specialist</div>
                                <div class="appointment-reason">Annual Heart Checkup</div>
                            </div>
                        </div>
                        <div class="status-badge status-pending">Pending</div>
                    </div>
                    
                    <div class="card-body">
                        <div class="info-item">
                            <span class="info-icon">📅</span>
                            <span class="info-text">Tuesday, Jul 09, 2025</span>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">⏰</span>
                            <span class="info-text">10:30 AM - 11:30 AM</span>
                            <span class="time-indicator">In 1 day</span>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">📍</span>
                            <span class="info-text">Heart Center, 3rd Floor</span>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">💰</span>
                            <span class="info-text">₱2,500 consultation</span>
                        </div>
                    </div>
                    
                    <div class="card-actions">
                        <button class="action-btn btn-primary">📞 Call Office</button>
                        <button class="action-btn btn-secondary">📝 Reschedule</button>
                        <button class="action-btn btn-secondary">🗺️ Directions</button>
                        <button class="action-btn btn-danger">❌ Cancel</button>
                    </div>
                </div>
            </div>
            
            <div class="appointment-card urgent">
                <div class="card-content">
                    <div class="card-header">
                        <div class="doctor-info">
                            <div class="doctor-avatar">KE</div>
                            <div class="doctor-details">
                                <h3>Dr. Kaloy E Enriquez</h3>
                                <div class="doctor-specialty">Cardiology • Heart Specialist</div>
                                <div class="appointment-reason">Follow-up: Blood Pressure Check</div>
                            </div>
                        </div>
                        <div class="status-badge status-confirmed">Confirmed</div>
                    </div>
                    
                    <div class="card-body">
                        <div class="info-item">
                            <span class="info-icon">📅</span>
                            <span class="info-text">Friday, Jul 18, 2025</span>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">⏰</span>
                            <span class="info-text">2:00 PM - 3:00 PM</span>
                            <span class="time-indicator soon">Starts in 2 hours</span>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">📍</span>
                            <span class="info-text">Heart Center, 3rd Floor</span>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">💊</span>
                            <span class="info-text">Bring current medications</span>
                        </div>
                    </div>
                    
                    <div class="card-actions">
                        <button class="action-btn btn-primary">✅ Check In</button>
                        <button class="action-btn btn-secondary">💬 Message Dr.</button>
                        <button class="action-btn btn-secondary">📋 Prep Form</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="appointment-section">
            <div class="section-header">
                <h2 class="section-title">Past Appointments</h2>
                <span class="section-count">0 this month</span>
            </div>
            
            <div style="text-align: center; padding: 40px; color: #64748b;">
                <div style="font-size: 48px; margin-bottom: 16px;">📅</div>
                <p>No past appointments this month</p>
            </div>
        </div>
    </div>
    
    <button class="new-appointment-btn">+</button>
    
    <script>
        // View toggle functionality
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Search functionality
        document.querySelector('.search-bar input').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            // In a real app, this would filter the appointments
            console.log('Searching for:', searchTerm);
        });
        
        // Action button functionality
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.textContent.trim();
                alert(`Action: ${action}`);
            });
        });
        
        // New appointment button
        document.querySelector('.new-appointment-btn').addEventListener('click', function() {
            alert('Opening new appointment form...');
        });
    </script>
</body>
</html>