// update de status van een afspraak in de database
function updateStatus(id, newStatus) {
    let getUrl = `DBjs.php?t=4&user=${USER}&pass=${PASS}&id=${id}&status=${newStatus}`;
    fetch(getUrl)
        .then(function(){console.log(`afspraak ${id} gezet naar status ${newStatus}`);});
}

// geef de afspraak- accept en deny-knoppen functionaliteit
function initAfspraken() {
    let afspraken = document.getElementsByClassName("afspraak");

    for (let i = 0; i < afspraken.length; i++) {
        let afId = afspraken[i].id;
        let afNumericId = afId.split('_')[1];
        let acceptBtn = afspraken[i].getElementsByClassName("afspraak-accept")[0];
        acceptBtn.addEventListener("click", function () {
            updateStatus(afNumericId, 1); // update afspraak-status -> 1 = geaccepteerd
            document.getElementById(afId).remove();
        });
        let denyBtn = afspraken[i].getElementsByClassName("afspraak-deny")[0];
        denyBtn.addEventListener("click", function () {
            updateStatus(afNumericId, 2); // update afspraak-status -> 2 = geweigerd
            document.getElementById(afId).remove();
        });
    }
}

// geeft string terug op basis van timestamp; 1234567 -> '11:45'
function getTimeDisplay(time) {
    let timeDate = new Date(time * 1000);
    let timeMin = timeDate.getMinutes() === 0 ? '00' : timeDate.getMinutes();
    return `${timeDate.getHours()}:${timeMin}`;
}

function getPeriodDisplay(start, end) {
    let startDisplay = getTimeDisplay(start);
    let endDisplay = getTimeDisplay(end);
    return `${startDisplay} - ${endDisplay}`;
}

function quickElement(elTag, elClass, elText) {
    let el = document.createElement(elTag);
    el.classList.add(elClass);
    el.innerText = elText;
    return el;
}

// maak div voor afspraak-overzicht
function createAfLink(afObj) {
    let afLink = document.createElement("div");
    afLink.classList.add('afspraak-overzicht-container');
    afLink.classList.add('adminComp');

    let disp = getPeriodDisplay(afObj['start'], afObj['end']);
    let tracker = afObj['tracker_id'];
    let url = 'afspraakTracker.php?af=' + tracker;

    afLink.appendChild(quickElement('p', 'afspraak-periode', disp));

    let mailLink = quickElement('a', 'afspraak-email', '');
    mailLink.setAttribute('href', `mailto:${afObj['email']}`);
    mailLink.innerHTML = "<i class='far fa-envelope'></i>";

    afLink.appendChild(mailLink);

    let details = quickElement('a', 'afspraak-link', 'details');

    details.setAttribute('href', url);
    details.innerHTML = "<i class='fas fa-info-circle'></i>";
    afLink.appendChild(details);

    document.getElementById('afspraken').appendChild(afLink);
}

function updateAfspraken(af) {
    let afDiv = document.getElementById('afspraken');
    afDiv.innerHTML = "";

    if (af.length > 0) {
        document.getElementById("afspraken").appendChild(quickElement('h2', 'overzicht-desc', 'Afspraken'))
    }

    for (let i = 0; i < af.length; i++) {
        createAfLink(af[i]);
    }
}

async function delDate(id) {
    let url = `DBjs.php?t=1&tId=${parseInt(id.split('_')[1])}&user=${USER}&pass=${PASS}`;
    await fetch(url)
        .then(function () {
            changeCalendar(currentMonth, currentYear);
        });

    console.log(`verwijderen van timeslot ${id}`);
}

// leuke woordgrap; genereer divjes om de openingstijden van een bepaalde dag weer te geven
function upDate(open) {
    document.getElementById("timeslot-overzicht").innerHTML = '';
    if (open.length > 0) {
        document.getElementById("timeslot-overzicht").appendChild(quickElement('h2', 'overzicht-desc', 'Openingstijden'))
    }

    for (let i = 0; i < open.length; i++) {
        let t = open[i];
        let timePeriod = getPeriodDisplay(parseInt(t['start']), parseInt(t['end']));

        let cal = quickElement('div', 'cal-timeslot-in', '');
        cal.appendChild(quickElement('p', 'cal-timeslot-display', timePeriod));
        let calDelContainer =   quickElement('a', 'timeslot-del', '');
        calDelContainer.id = "open_" + t['id'];
        let calDel = quickElement('i', "far", '');
        calDel.classList.add('fa-trash-alt');
        calDel.addEventListener('click', function () {
            delDate(calDel.id);
            cal.remove();
        })
        calDelContainer.appendChild(calDel);
        cal.appendChild(calDelContainer);

        document.getElementById("timeslot-overzicht").appendChild(cal);
    }
}

// haal afspraken van bepaalde dag op
async function updateAfspraakData(year, month, day) {
    let date = `${day}-${month}-${year}`;
    let url = `DBjs.php?t=0&d=${date}&user=${USER}&pass=${PASS}`;

    await fetch(url)
        .then(response => response.json())
        .then(data => updateAfspraken(data));
}

// haal openingstijden van bepaalde dag op
async function updateOpeningstijdData(year, month, day) {
    let date = `${day}-${month}-${year}`;
    let url = `DBjs.php?t=2&d=${date}`;

    await fetch(url)
        .then(response => response.json())
        .then(data => upDate(data));
}

async function delOpen(year, month, day) {
    let date = `${day}-${month}-${year}`;
    let url = `DBjs.php?t=8&d=${date}&user=${USER}&pass=${PASS}`;
    await fetch(url)
        .then(function () {
            changeCalendar(currentMonth, currentYear);
        });
}

async function getDate(year, month, day) {
    if (document.getElementById('do-erase').checked) {
        await delOpen(year, month, day);
    } else {
        await updateAfspraakData(year, month, day);
        await updateOpeningstijdData(year, month, day);
    }
    return 1;
}

// voer een fetch uit met get-variabelen om een openingstijd toe te voegen aan de db
async function addOpen(date, start, end) {
    let startTime = new Date(date + ' ' + start).getTime() / 1000;
    let endTime = new Date(date + ' ' + end).getTime() / 1000;
    let url = `DBjs.php?t=6&start=${startTime}&end=${endTime}&user=${USER}&pass=${PASS}`;

    await fetch(url)
        .then(response => console.log(response.url));
}

async function addOpenRepeat(date, start, end, rType, rCount) {
    let startTime = new Date(date + ' ' + start).getTime() / 1000;
    let endTime = new Date(date + ' ' + end).getTime() / 1000;
    let url = `DBjs.php?t=7&start=${startTime}&end=${endTime}&user=${USER}&pass=${PASS}&rCount=${rCount}&rType=${rType}`;

    await fetch(url)
        .then(response => console.log(response.url));
}

// voert JS-fetch uit om nieuwe afspraak toe te voegen
async function addPress() {
    let repeatCheckbox = document.getElementById('do-repeat');
    let start = document.getElementById('time-start').value;
    let end = document.getElementById('time-end').value;
    let date = document.getElementById('time-date').value;
    if (repeatCheckbox.checked) {
        let repeatType = document.getElementById('time-repeat-type').value;
        let repeatCount = document.getElementById('time-repeat-amount').value;
        await addOpenRepeat(date, start, end, repeatType, repeatCount);
    } else await addOpen(date, start, end);
}

// voeg of haal de deleter class weg bij alle datecells
function updateErase() {
    let eraser = document.getElementById('do-erase-label');
    if (document.getElementById('do-erase').checked) {
        eraser.classList.add('selected');
        forEachDatecell(function (cell) {
            cell.classList.add('deleter');
            cell.classList.remove('selected');
        });
    } else {
        eraser.classList.remove('selected');
        forEachDatecell(function (cell) {
            cell.classList.remove('deleter');
        });
    }

    let overzichten = document.getElementsByClassName('overzicht');
    for (let i = 0; i < overzichten.length; i++) {
        overzichten[i].innerHTML = '';
    }
}

// init eventlistener voor de gum
function initErase() {
    document.getElementById('do-erase').addEventListener('change', function () {
        updateErase();
    });
}

// eventlistener aan openingstijd-add-knop
function initAdder() {
    document.getElementById('afpsraak-adder-btn').addEventListener("click", async function () {
        await addPress()
            .then(function () {
                changeCalendar(currentMonth, currentYear);
            });
    });
}

// laat de repeat-afspraak div zien of niet op basis van de repeat knop
function initRepeat() {
    let repeatCheckbox = document.getElementById('do-repeat');
    repeatCheckbox.addEventListener("change", function () {
        if (repeatCheckbox.checked) {
            document.getElementById('repeat-div').style.display = 'flex';
            document.getElementById('do-repeat-label').classList.add('selected');
        } else {
            document.getElementById('repeat-div').style.display = 'none';
            document.getElementById('do-repeat-label').classList.remove('selected');
        }
    });
}
// overschreven om updateErase aan het einde toe te voegen
function changeCalendar(month, year) {
    let date = `1-${month + 1}-${year}`;
    let url = `DBjs.php?t=3&d=${date}`;

    fetch(url)
        .then(response => response.json())
        .then(data => renderCalendar(month, year, data))
        .then(function () {
            updateErase()
        });
}

// gum moet aan de bovenkant komen
function initCalEraser() {
    let calHeader = document.getElementById('kalender-header');
    const eraser = "<label id='do-erase-label' for='do-erase'><i class='fas fa-eraser'></i></label><input type='checkbox' id='do-erase'>";
    let tempHTML = calHeader.innerHTML;
    calHeader.innerHTML = tempHTML + eraser;
    console.log('ja');
}

initCalEraser();
initAfspraken();
initErase();
initAdder();
initRepeat();