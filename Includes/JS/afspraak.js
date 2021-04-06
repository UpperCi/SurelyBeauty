// Dankjewel https://medium.com/@nitinpatel_20236/challenge-of-building-a-calendar-with-pure-javascript-a86f1303267d

async function getDate(year, month, day) {
    let date = `${day}-${month}-${year}`;
    let url = `DBjs.php?t=2&d=${date}`;

    await fetch(url)
        .then(response => response.json())
        .then(data => upDate(data));
}


// misschien had een 'forEachRadio' overzichtelijker geweest maar het wordt toch maar 1 keer gebruikt
// geeft net zoals setSelected een knop aan om vervolgens met CSS overzichtelijk te markeren
function radioChange() {
    let fullList = document.getElementById("time-select");
    let listElements = fullList.childNodes;
    for (let i = 0; i < listElements.length; i++) {
        let el = listElements[i];
        let elChildren = el.childNodes;
        for (let j = 0; j < elChildren.length; j++) {
            let child = elChildren[j];
            if (child.tagName === "INPUT") {
                if (child.checked) {
                    el.classList.add("selected");
                } else {
                    el.classList.remove("selected");
                }
            }
        }
    }
}

// maakt een [11:30] knop en voegt het toe aan #time-select (11:30 is dynamisch, uiteraard)
function createTimeRadio(time) {
    let timeDate = new Date(time * 1000);
    let timeMin = timeDate.getMinutes() === 0 ? '00' : timeDate.getMinutes();
    let timeDisplay = `${timeDate.getHours()}:${timeMin}`;
    let timeId = `time-${timeDisplay}`;

    let listItem = document.createElement("li");
    listItem.setAttribute("for", timeId);

    let radio = document.createElement("input");
    radio.setAttribute("type", "radio");
    radio.setAttribute("class", "timeRadio");
    radio.setAttribute("name", "selectedTime");
    radio.setAttribute("id", timeId);
    radio.setAttribute("value", time);
    radio.addEventListener("change", radioChange);

    let label = document.createElement("label");
    label.textContent = timeDisplay;
    label.setAttribute("for", timeId);

    listItem.appendChild(radio);
    listItem.appendChild(label);
    document.getElementById("time-select").appendChild(listItem);
}

// voegt de knoppen toe om te kiezen hoe laat je een afspraak wilt maken
function upDate(open) {
    const timeInterval = 1800; // hoeveel seconden moeten er tussen elke twee knoppen zitten

    for (let i = 0; i < open.length; i++) {
        let t = open[i];
        let tStart = parseInt(t['start']);
        let tEnd = parseInt(t['end']);

        let tDiff = tEnd - tStart;
        let tIter = Math.floor(tDiff / timeInterval);

        document.getElementById("time-select").innerHTML = "";
        for (let i = 0; i < tIter; i++) {
            createTimeRadio(tStart + i * timeInterval);
        }

        let start = new Date(tStart * 1000);
        let end = new Date(tEnd * 1000);

        document.getElementById("afspraak-selected-date").textContent = `${start.getDate()} ${months[start.getMonth()]}`;

        console.log(`Op ${start.getDate()} ${months[start.getMonth()]} open van ${start.getHours()} tot ${end.getHours()}`)
    }
}

// func(checkbox, behandeling) wordt op elke behandeling-checkbox uitgevoerd
function forEachCheck(func) {
    let behandelingen = document.getElementsByClassName("behandeling");
    for (let i = 0; i < behandelingen.length; i++) {
        let behandeling = behandelingen[i];
        let behandelChildren = behandeling.childNodes;
        for (let j = 0; j < behandelChildren.length; j++) {
            let checkbox = behandelChildren[j]
            if (checkbox.tagName === "INPUT") {
                func(checkbox, behandeling);
            }
        }
    }
}

// geef element aan om met CSS te markeren
function checkChange() {
    forEachCheck(function (check, checkParent) {
        if (check.checked) {
            checkParent.classList.add("selected");
        } else {
            checkParent.classList.remove("selected");
        }
    });
}

// initialize afspraak-checkbox eventListeners
function initChecks() {
    forEachCheck(function (check) {
        check.addEventListener("change", checkChange);
    });
}

initChecks();
