<?php
header('Content-Type: application/json');

// REGULAR HOLIDAYS (Nationwide - Government and Private Offices Closed)
$regular_holidays = [
    "2025-01-01", // New Year's Day
    "2025-04-17", // Maundy Thursday
    "2025-04-18", // Good Friday
    "2025-04-09", // Araw ng Kagitingan (Day of Valor)
    "2025-05-01", // Labor Day
    "2025-06-12", // Independence Day
    "2025-08-25", // National Heroes Day (Last Monday of August)
    "2025-11-30", // Bonifacio Day
    "2025-12-25", // Christmas Day
    "2025-12-30", // Rizal Day
    "2025-03-31", // Eid al-Fitr (End of Ramadan)
    "2025-06-07", // Eid al-Adha (Feast of the Sacrifice)
];

// SPECIAL NON-WORKING DAYS (Nationwide - Government offices closed, private may operate)
$special_non_working_days = [
    "2025-02-09", // Chinese New Year
    "2025-04-19", // Black Saturday
    "2025-08-21", // Ninoy Aquino Day
    "2025-11-01", // All Saints' Day
    "2025-11-02", // All Souls' Day
    "2025-12-08", // Feast of the Immaculate Conception
    "2025-12-24", // Christmas Eve
    "2025-12-31", // New Year's Eve
    "2025-02-22", // EDSA People Power Revolution Anniversary
    "2025-04-21", // Easter Monday (may be declared)
    "2025-07-07", // Amun Jadid (Islamic New Year)
    "2025-11-03", // Additional All Saints/Souls Day (may be declared)
    "2025-12-26", // Additional Christmas holiday (may be declared)
];

// LOCAL/REGIONAL HOLIDAYS (add based on your location)
$local_holidays = [
    "2025-06-24", // Manila Day
    "2025-08-19", // Quezon City Day
    "2025-02-23", // Makati Day
    "2025-08-14", // Cebu City Charter Day
    "2025-03-01", // Davao City Day
    "2025-08-17", // Kadayawan Festival (Davao)
    "2025-01-25", // Dinagyang Festival (Iloilo)
    "2025-02-01", // Panagbenga Festival (Baguio)
];

// Combine all holidays
$all_holidays = array_merge($regular_holidays, $special_non_working_days, $local_holidays);
$all_holidays = array_unique($all_holidays);
sort($all_holidays);

// Example booked dates - replace with your actual database queries
$booked_dates = ["2025-07-10", "2025-07-12"];

echo json_encode([
    "booked" => $booked_dates,
    "holidays" => $all_holidays,
    "holiday_categories" => [
        "regular" => $regular_holidays,
        "special_non_working" => $special_non_working_days,
        "local" => $local_holidays
    ]
]);
?>

<script>
  // After calendar.render();
  fetch('get_booked_and_holidays.php')
    .then(res => res.json())
    .then(data => {
      // data.booked = ['2025-07-10', ...]
      // data.holidays = ['2025-07-12', ...]
      console.log('Loaded Philippine holidays:', data);
      
      setTimeout(() => {
        document.querySelectorAll('.fc-daygrid-day').forEach(cell => {
          const date = cell.getAttribute('data-date');
          
          // Add booked date styling
          if (data.booked.includes(date)) {
            cell.classList.add('booked-date');
          }
          
          // Add holiday styling with categories
          if (data.holidays.includes(date)) {
            cell.classList.add('holiday-date');
            
            // Add specific holiday category classes
            if (data.holiday_categories.regular.includes(date)) {
              cell.classList.add('regular-holiday');
            } else if (data.holiday_categories.special_non_working.includes(date)) {
              cell.classList.add('special-non-working');
            } else if (data.holiday_categories.local.includes(date)) {
              cell.classList.add('local-holiday');
            }
            
            // Add holiday type as data attribute for custom styling
            cell.setAttribute('data-holiday-type', 
              data.holiday_categories.regular.includes(date) ? 'regular' :
              data.holiday_categories.special_non_working.includes(date) ? 'special' : 'local'
            );
          }
        });
      }, 100); // Wait for calendar to render
    })
    .catch(error => {
      console.error('Error loading Philippine holidays:', error);
    });
</script>