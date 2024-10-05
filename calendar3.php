<?php
// Read the JSON file using PHP
$jsonData = file_get_contents('data.json'); // Ensure the path to your JSON file is correct
$data = json_decode($jsonData, true); // Decode JSON into an associative array
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar in a Div</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <style>
        #calendar-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #687dd1;
        }
        #calendar {
            max-width: 100%;
            background-color: white;
        }
    </style>
</head>
<body>

    <div id="calendar-container">
        <select id="person-select">
            <option value="none">Select a person</option>
            <?php
            // Extract unique names and populate the dropdown dynamically
            $names = [];
            foreach ($data as $item) {
                if (!in_array($item['Name'], $names)) {
                    $names[] = $item['Name'];
                    echo "<option value='{$item['Name']}'>{$item['Name']}</option>";
                }
            }
            ?>
        </select>
        <div id='calendar'></div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script>


        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var personSelect = document.getElementById('person-select');

            // PHP passes the data directly into JavaScript as a JSON object
            var data = <?php echo json_encode($data); ?>;

            // Transform the data to FullCalendar event format
            var allEvents = data.map(item => {
                const dateParts = item.date.split('/');
                const year = dateParts[2];
                const month = dateParts[1] - 1; // FullCalendar uses 0-indexed months
                const day = dateParts[0];

                
                return [
                    {
                        title:'IN',
                        start: new Date(year, month, day, ...item.IN.split(':')),
                        color: "#35cc51",
                        name: item.Name
                    },
                    {
                        title: 'OUT',
                        start: new Date(year, month, day, ...item.OUT.split(':')),
                        color: "#FF5733",
                        name: item.Name
                    }
                ];
            }).flat();

            // Initialize FullCalendar
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                // Start with an empty calendar
                events: []
            });

            // Render the calendar
            calendar.render();

            // Handle the change event for the dropdown
            personSelect.addEventListener('change', function() {
                var selectedPerson = personSelect.value;
                calendar.removeAllEvents(); // Clear any existing events

                // Filter and display only the selected person's events
                if (selectedPerson !== 'none') {
                    var filteredEvents = allEvents.filter(event => event.name === selectedPerson);
                    calendar.addEventSource(filteredEvents); // Add filtered events to the calendar
                }
            });
        });
    </script>

</body>
</html>
