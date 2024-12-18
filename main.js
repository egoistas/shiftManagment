// main.js

// Πλοήγηση
function navigateToLogout() {
    window.location.href = 'logout.php';
}

function navigateToHome() {
    window.location.href = 'index.php';
}

function navigateToReportPanel() {
    window.location.href = 'report_panel.php';
}

// Φόρτωση επιλογών έτους κατά την εκκίνηση της σελίδας
window.onload = function() {
    const yearSelect = document.getElementById('year-select');
    const currentYear = new Date().getFullYear();
    for (let i = currentYear - 5; i <= currentYear + 5; i++) {
        const option = document.createElement('option');
        option.value = i;0
        option.textContent = i;
        if (i === currentYear) {
            option.selected = true;
        }
        yearSelect.appendChild(option);
    }

    // Event listeners
    document.getElementById('month-select').addEventListener('change', loadPersonnel);
    document.getElementById('year-select').addEventListener('change', loadPersonnel);
    document.getElementById('post-select').addEventListener('change', loadPersonnel);
    document.getElementById('personnel-a').addEventListener('change', function() {
        loadDates(this.value, 'dates-a');
        updatePersonnelB();
    });
    document.getElementById('personnel-b').addEventListener('change', function() {
        loadDates(this.value, 'dates-b');

    });

    loadInitialData(); // Φόρτωση αρχικών δεδομένων
};


function loadInitialData() {
    const post = document.getElementById('post-select').value;
    const month = document.getElementById('month-select').value;
    const year = document.getElementById('year-select').value;

    if (post && month && year) {
        loadPersonnel(); // Φόρτωση του προσωπικού όταν η σελίδα φορτώνει
    }
}
// edo i sinartisi apeuthinetai stin fortosi prosopikou apo to backend kai kanei kai polla alla
function loadPersonnel() {
    const post = document.getElementById('post-select').value;
    const month = document.getElementById('month-select').value;
    const year = document.getElementById('year-select').value;

    if (post && month !== '' && year !== '') {
        fetch(`get_personnel.php?post=${post}&month=${month}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                const personnelSelect = document.getElementById('personnel-a');
                personnelSelect.innerHTML = '<option value="">Επιλέξτε άτομο</option>';

                if (Array.isArray(data)) {
                    data.forEach(person => {
                        const option = document.createElement('option');
                        option.value = person.nameid;
                        option.textContent = `${person.nameid} ${person.rankName} ${person.name} ${person.surname}`;
                        personnelSelect.appendChild(option);
                    });
                } else {
                    console.error('Unexpected response format:', data);
                }
                console.log('mpika sto loadpersonel');
                updatePersonnelB(); // Ενημέρωση του Ατόμου Β μετά την ενημέρωση του Ατόμου Α
            })
            .catch(error => console.error('Error:', error));
    }
}



function updateDateSelect() {
    const personId = document.getElementById('personnel-a').value;
    const post = document.getElementById('post-select').value;
    const month = document.getElementById('month-select').value;
    const year = document.getElementById('year-select').value;

    if (personId && post && month !== '' && year !== '') {
        fetch(`get_dates.php?person=${personId}&post=${post}&month=${month}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                const dateSelect = document.getElementById('dates-a');
                dateSelect.innerHTML = '<option value="">Επιλέξτε ημερομηνία</option>';
                
                data.forEach(date => {
                    const option = document.createElement('option');
                    option.value = `${date.date}-${date.person}`;
                    option.textContent = `${date.date} - ${date.person}`;
                    dateSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
    }
}
// edo ginetai to update tou deuterou prosopou basi to postid poy xrisimopoiisa stin epilogi

function updatePersonnelB() {
    const personA = document.getElementById('personnel-a').value;
    const post = document.getElementById('post-select').value;
    const month = document.getElementById('month-select').value;
    const year = document.getElementById('year-select').value;

    if (post && month !== '' && year !== '') {
        fetch(`get_personnel_b.php?post=${post}&month=${month}&year=${year}&exclude=${personA}`)
            .then(response => response.json())
            .then(data => {
                const personnelBSelect = document.getElementById('personnel-b');
                personnelBSelect.innerHTML = '<option value="">Επιλέξτε άτομο</option>';
                
                data.forEach(person => {
                    const option = document.createElement('option');
                    option.value = person.nameid;
                    option.textContent = `${person.nameid} ${person.rankName} ${person.name} ${person.surname}`;
                    personnelBSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
    }
}

function loadDates(personId, dateSelectId) {
    const post = document.getElementById('post-select').value;
    const month = document.getElementById('month-select').value;
    const year = document.getElementById('year-select').value;

    if (personId && post && month !== '' && year !== '') {
        fetch(`get_dates.php?person=${personId}&post=${post}&month=${month}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                const dateSelect = document.getElementById(dateSelectId);
                dateSelect.innerHTML = '<option value="">Επιλέξτε ημερομηνία</option>';
                
                data.forEach(date => {
                    const option = document.createElement('option');
                    option.value = date.date;
                    option.textContent = date.date;
                    dateSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
    }
}

// edo teleinei i parania aka javascript