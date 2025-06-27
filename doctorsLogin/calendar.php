<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Interactive Calendar with Holidays</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      padding: 20px;
    }
    .calendar {
      max-width: 700px;
      margin: auto;
    }
    select {
      padding: 5px 10px;
      margin: 10px;
      font-size: 16px;
    }
    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 5px;
      margin-top: 10px;
    }
    .day, .date {
      padding: 10px;
      background: #f0f0f0;
      border-radius: 5px;
    }
    .date {
      min-height: 80px;
      text-align: left;
      background: #fff;
      border: 1px solid #ddd;
      position: relative;
    }
    .today {
      background-color: #d1e7dd;
    }
    .holiday {
      color: red;
      font-size: 0.85em;
      margin-top: 5px;
      display: block;
    }
    .day {
      font-weight: bold;
      background: #007bff;
      color: #fff;
    }
  </style>
</head>
<body>

<div class="calendar">
  <h2>Interactive Calendar with Holidays</h2>
  <div>
    <label for="month">Month:</label>
    <select id="month"></select>

    <label for="year">Year:</label>
    <select id="year"></select>
  </div>

  <div class="calendar-grid" id="calendar">
    <div class="day">Sun</div><div class="day">Mon</div><div class="day">Tue</div>
    <div class="day">Wed</div><div class="day">Thu</div><div class="day">Fri</div><div class="day">Sat</div>
  </div>
</div>

<script>
  const calendar = document.getElementById('calendar');
  const monthSelect = document.getElementById('month');
  const yearSelect = document.getElementById('year');

  const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];

  // Holidays format: 'MM-DD': 'Holiday Name'
  const holidays = {
    '01-01': "New Year's Day",
    '02-14': "Valentine's Day",
    '04-09': "Araw ng Kagitingan",
    '06-12': "Independence Day",
    '11-01': "All Saints' Day",
    '12-25': "Christmas Day",
    '12-31': "New Year's Eve"
  };

  // Fill month and year dropdown
  for (let i = 0; i < 12; i++) {
    const opt = document.createElement('option');
    opt.value = i;
    opt.text = monthNames[i];
    monthSelect.appendChild(opt);
  }

  const currentYear = new Date().getFullYear();
  for (let y = currentYear - 5; y <= currentYear + 5; y++) {
    const opt = document.createElement('option');
    opt.value = y;
    opt.text = y;
    yearSelect.appendChild(opt);
  }

  // Default to today
  const today = new Date();
  monthSelect.value = today.getMonth();
  yearSelect.value = today.getFullYear();

  function renderCalendar(month, year) {
    // Clear previous dates
    const allDates = calendar.querySelectorAll('.date');
    allDates.forEach(date => date.remove());

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Add blank spaces
    for (let i = 0; i < firstDay; i++) {
      const empty = document.createElement('div');
      calendar.appendChild(empty);
    }

    // Add dates
    for (let day = 1; day <= daysInMonth; day++) {
      const date = document.createElement('div');
      date.classList.add('date');

      const dateStr = `${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

      date.innerHTML = `<strong>${day}</strong>`;
      if (holidays[dateStr]) {
        date.innerHTML += `<span class="holiday">${holidays[dateStr]}</span>`;
      }

      if (
        day === today.getDate() &&
        month === today.getMonth() &&
        year === today.getFullYear()
      ) {
        date.classList.add('today');
      }

      calendar.appendChild(date);
    }
  }

  // Initial render
  renderCalendar(parseInt(monthSelect.value), parseInt(yearSelect.value));

  // Event listeners
  monthSelect.addEventListener('change', () => {
    renderCalendar(parseInt(monthSelect.value), parseInt(yearSelect.value));
  });
  yearSelect.addEventListener('change', () => {
    renderCalendar(parseInt(monthSelect.value), parseInt(yearSelect.value));
  });
</script>

</body>
</html>
