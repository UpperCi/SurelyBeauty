// Dankjewel https://medium.com/@nitinpatel_20236/challenge-of-building-a-calendar-with-pure-javascript-a86f1303267d
let today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();
let currentDay = today.getDay();

const months = ["Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December"];

changeCalendar(currentMonth, currentYear);

function initCalendarButtons() {
    document.getElementById("kalender-prev").addEventListener("click", previous);
    document.getElementById("kalender-next").addEventListener("click", next);
}

function next() {
    currentYear = (currentMonth === 11) ? currentYear + 1 : currentYear;
    currentMonth = (currentMonth + 1) % 12;
    changeCalendar(currentMonth, currentYear);
}

function previous() {
    currentYear = (currentMonth === 0) ? currentYear - 1 : currentYear;
    currentMonth = (currentMonth === 0) ? 11 : currentMonth - 1;
    changeCalendar(currentMonth, currentYear);
}

function changeCalendar(month, year) {
    let date = `1-${month + 1}-${year}`;
    let url = `DBjs.php?t=3&d=${date}`;

    fetch(url)
        .then(response => response.json())
        .then(data => renderCalendar(month, year, data));
}

// gooi table met rijen, kolommen als weken, weekdagen in #kalender-body op basis van maand en jaar
// grotendeels van het artikel bovenaan gecomment
function renderCalendar(month, year, monthdays) {
    let firstDay = (new Date(year, month)).getDay();
    let tbl = document.getElementById("kalender-body");
    let date = 1;

    tbl.innerHTML = "";

    document.getElementById("kalender-date-desc").textContent = `${months[month]} ${year}`;

    for (let i = 0; i < 6; i++) {
        let row = document.createElement("tr");
        for (let j = 0; j < 7; j++) {
            if (i === 0 && j < firstDay) {
                cell = document.createElement("td");
                cellText = document.createTextNode("");
                cell.appendChild(cellText);
                row.appendChild(cell);
            } else if (date > daysInMonth(month, year)) {
                break;
            } else {
                let cellOuter = document.createElement("td");
                let cell = document.createElement("input");
                cell.setAttribute("type", "button");
                let cellId = `${date}`;
                let cellOpen = monthdays[date - 1];
                cell.setAttribute("value", date.toString());

                cell.setAttribute("id", cellId);
                if (cellOpen) { // indien er vandaag openingstijden zijn
                    cell.setAttribute("class", "dateCellOpen");
                    cell.addEventListener("click", function () {
                        getDate(year, month + 1, cellId);
                        currentDay = cellId;
                        setSelected(cellId);
                    })
                } else {
                    cell.setAttribute("class", "dateCellClosed");
                    cell.setAttribute("disabled", "");
                }

                cellOuter.appendChild(cell);
                row.appendChild(cellOuter);
                date++;
            }

        }
        tbl.appendChild(row);
    }
}

function daysInMonth(iMonth, iYear) {
    let tempDate = new Date(iYear, iMonth, 32).getDate();
    return 32 - tempDate;
}

// func(cell, row) wordt op elke geldige cel van de kalender uitgevoerd
function forEachDatecell(func) {
    let cal = document.getElementById("kalender-body");
    let calRows = cal.childNodes;
    for (let i = 0; i < calRows.length; i++) {
        let row = calRows[i];
        let cells = row.childNodes;
        for (let j = 0; j < cells.length; j++) {
            let cell = cells[j];
            if (cell.children.length > 0) {
                func(cell.children[0], row);
            }
        }
    }
}

// update welke cell er geselecteerd staat zodat deze met CSS kan worden gemarkeerd
// overzichtelijker dan '7 februari' uitschrijven en de gebruiker laten lezen
function setSelected(id) {
    forEachDatecell(function (cell) {
        if (cell.id === id) {
            console.log(id)
            cell.classList.add('selected');
        } else cell.classList.remove('selected');
    });
}

initCalendarButtons();
