let date = new Date(),
currYear = date.getFullYear(),
currMonth = date.getMonth();

let selectedDate = date;
let displayedDates = null;

const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
const short_months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
const weekday_labels = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
const sunday_index = 0;
let first_day_of_week = "Sun";
let index_first_day = weekday_labels.indexOf(first_day_of_week);

// function changeFirstDay(){
//     console.log(first_day_of_week);
//     first_day_of_week = document.getElementById("day-of-week").value;
//     index_first_day = weekday_labels.indexOf(first_day_of_week);
//     renderCalender();
//     clickDate(`curr_${date.getDate()}`);
// }

function getCSSvariable(var_name){
    return getComputedStyle(document.body).getPropertyValue(var_name);
}

/**
 * Gets the index of week with day `id`.
 * @param {Array} array Array of all month days
 * @param {string} id ID of day in week we want to obtain
 * @returns Index of week containing day with `id`
 */
function getWeek(array, id){
    for (let i = 0; i < array.length; i+=7){
        for (let j = 0; j < 7; j++){
            if (array[i + j].id == id){
                return i;
            } 
        }    
    }
}

/**
 * Updates the currently shown week
 * @param {string} id ID of clicked date
 */
function clickDate(id){
    var numDays = 0;
    var viewSelectorText = document.getElementById("view-selector").innerHTML;
    if (viewSelectorText.includes('Week')) numDays = 7;
    if (viewSelectorText.includes('Day')) numDays = 1;
    let calendarDaysPlaceholder = document.getElementById("days");
    let calendarDays = calendarDaysPlaceholder.getElementsByTagName("li");

    // Changing the `selected` class to the clicked item.
    [...calendarDays].forEach(day => {
        if (day.id == id){
            day.classList.add("selected");
        }
        else {
            day.classList.remove("selected");
        }
    });
    
    var week_id = getWeek(calendarDays, id);    
    var currentWeekDays = [...calendarDays].slice(week_id,week_id+7);
    
    let weekDays = document.getElementById("weekly-days");
    weekDays.innerHTML = '';
    
    // Rendering of the weekly view
    var contains_other_month = false;
    var before = false;

    var day = id.split('_')[1];
    var monthChange = getMonthStatusFromId(id, monthChange);
    selectedDate = new Date(currYear, currMonth + monthChange, day);

    if (numDays == 7){
        ({ contains_other_month, before } = generateWeekly_Days_Labels(numDays, currentWeekDays, contains_other_month, before, weekDays));
        displayedDates = currentWeekDays;
    }
    else if (numDays == 1){
        ({ contains_other_month, before } = generateDailyDays_Labels(numDays, contains_other_month, before, weekDays, id));
        displayedDates = document.getElementById(id);
    }

    // Updates the month title
    if (viewSelectorText.includes('Week')) updateMonthTitle(id, contains_other_month, before);
    if (viewSelectorText.includes('Day')) updateMonthTitle(id, false, false);
}

function generateDailyDays_Labels(numDays, contains_other_month, before, weekDays, clickedDayID){
    var labelHolder = document.getElementById('weekly-calendar-label');
    var day = document.getElementById(clickedDayID);
    var clickedDay = selectedDate;
    labelHolder.innerHTML = `<li class='alone'>${weekday_labels[clickedDay.getDay()]}</li>`;

    changeClassOfWeekdayLabel(labelHolder, 0, day, true);
    day.classList.add('alone');
    ({ contains_other_month, before } = cloneDay(day, contains_other_month, before, 0, weekDays));
    return { contains_other_month, before };
}

function generateWeekly_Days_Labels(numDays, currentWeekDays, contains_other_month, before, weekDays){
    var labelHolder = document.getElementById('weekly-calendar-label');
    labelHolder.innerHTML = "";
    for (let i = 0; i < numDays; i++){
        var day = currentWeekDays[i];
        
        if (day.classList.contains('alone')){
            day.classList.remove('alone');
        }
        labelHolder.innerHTML += `<li>${weekday_labels[i]}</li>`;
        changeClassOfWeekdayLabel(labelHolder, i, day);
        ({ contains_other_month, before } = cloneDay(day, contains_other_month, before, i, weekDays));
    }
    return { contains_other_month, before };
}

function cloneDay(day, contains_other_month, before, i, weekDays){
    var copy = day.cloneNode(true);
    if (copy.classList.contains('inactive')){
        copy.classList.remove('inactive');
        contains_other_month = true;
        before = !before ? i == 0 : before;
    }
    weekDays.appendChild(copy);
    return { contains_other_month, before };
}

function changeClassOfWeekdayLabel(labelHolder, i, day, alone=false){
    var label = labelHolder.getElementsByTagName("li")[i];
    if (day.classList.contains('active')){
        label.className = 'active';
    }
    else if (day.classList.contains('selected')){
        label.className = 'selected';
    }
    else{
        label.className = '';
    }

    if (alone && !label.classList.contains('alone')){
        label.classList.add('alone');
    }
    else {
        label.classList.remove('alone');
    }
}

function updateMonthTitle(id, contains_other_month, before){
    var str = "";
    if (contains_other_month){
        var month = before ? currMonth - 1 : currMonth + 1;
        var year = month < 0 || month > 11 ? new Date(currYear, month).getFullYear() : currYear;
        var month = month < 0 || month > 11 ? new Date(currYear, month).getMonth() : month;

        str = before ? `${short_months[month]} ${year} - ${short_months[currMonth]} ${currYear}` : `${short_months[currMonth]} ${currYear} - ${short_months[month]} ${year}`;
    }
    else {
        str = `${months[selectedDate.getMonth()]} ${selectedDate.getFullYear()}`;
    }
    document.getElementById("current-week").innerHTML = str;
}

function getMonthStatusFromId(id, monthChange) {
    switch (id.split('_')[0]) {
        case 'curr':
            monthChange = 0;
            break;
        case 'prev':
            monthChange = -1;
            break;
        case 'next':
            monthChange = 1;
            break;
        default:
            monthChange = 0;
            break;
    }
    return monthChange;
}

/**
 * Renders the calendar in sidebar
 */
function renderCalender(){
    let firstDay = new Date(currYear, currMonth, 1).getDay(),
    lastDate = new Date(currYear, currMonth+1, 0).getDate(),
    lastDay = new Date(currYear, currMonth, lastDate).getDay()
    lastDateLastMonth = new Date(currYear, currMonth, 0).getDate();

    let liTags = "";
    for (let i = firstDay; i > index_first_day; i--){
        liTags += `<li class='inactive' id='prev_${lastDateLastMonth- i + 1}' onclick='clickDate(this.id)'>${lastDateLastMonth- i + 1}</li>`;
    }

    for (let i = 1; i <= lastDate; i++){
        let isToday = i === date.getDate() && currMonth === new Date().getMonth() && currYear === new Date().getFullYear() ? "active" : "";

        liTags += `<li class='${isToday}' id='curr_${i}' onclick='clickDate(this.id)'>${i}</li>`
    }

    for (let i = lastDay; i < (6 + index_first_day) % 7; i++){
        liTags += `<li class='inactive' id='next_${i - lastDay + 1}' onclick='clickDate(this.id)'>${i - lastDay + 1}</li>`;
    }

    document.getElementById("current-month").innerHTML = `${months[currMonth]} ${currYear}`;
    document.getElementById("days").innerHTML = liTags;
}

/**
 * Moves calendar back to today.
 */
function toToday(){
    date = new Date();
    currMonth = date.getMonth();
    currYear = date.getFullYear();
    renderCalender();
    var viewSelectorText = document.getElementById("view-selector").innerHTML;
    clickDate(`curr_${date.getDate()}`);
}

/**
 * Renders the times on the side of the weekly view.
 */
function renderTimes(){
    var times = document.getElementById("timestamps");
    times.innerHTML += `<div><span>12 AM</span></div>`;
    for (let i = 1; i <= 12; i++){
        times.innerHTML += `<div><span>${i} AM</span></div>`;
    }
    for (let i = 1; i < 12; i++){
        times.innerHTML += `<div><span>${i} PM</span></div>`
    }

    var sep = document.getElementById('time-sep');
    for (let i = 0; i < 24; i++){
        sep.innerHTML += `<div class='sep'></div>`
    }

    var viewSelector = document.getElementById("view-selector");
    if (viewSelector.innerHTML.includes("Week")){
        generateTimeCols(7);
    }
    if (viewSelector.innerHTML.includes("Day")){
        generateTimeCols(1);
    }
}

/**
 * On window load function.
 */
function onLoad(){
    prevNextIcons = document.querySelectorAll(".icons span");
    prevNextIcons.forEach(icon => {
        if (!icon.id.includes("week")){
            icon.addEventListener("click", () => {
                currMonth = icon.id === "prev" ? currMonth - 1 : currMonth + 1;
                if (currMonth < 0 || currMonth > 11){
                    date = new Date(currYear, currMonth, new Date().getDate());
                    currYear = date.getFullYear();
                    currMonth = date.getMonth();
                } else {
                    date = new Date();
                }
                renderCalender();
            });
        }
        else {
            icon.addEventListener("click", () => {
                var viewSelectorText = document.getElementById("view-selector").innerHTML;
                var daysToAdd = 0;
                if (viewSelectorText.includes('Week')) daysToAdd = 7;
                if (viewSelectorText.includes('Day')) daysToAdd = 1;

                numDays = icon.id === "prev_week" ? -daysToAdd : daysToAdd;
                var newDay = selectedDate.getDate() + numDays;
                selectedDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), newDay);
                currMonth = selectedDate.getMonth();
                currYear = selectedDate.getFullYear();

                renderCalender();
                clickDate(`curr_${selectedDate.getDate()}`);
            });
        }
    });
    renderCalender();
    clickDate(`curr_${date.getDate()}`);
    renderTimes();
}

window.addEventListener('load', onLoad);

// TODO: FINISH THIS BS
/**
 * Function to show events in the calendar.
 * @param {string} eventTitle Name of the event
 * @param {string} eventDesc Description of the event
 * @param {Date} eventStartTime Start time of event
 * @param {Date} eventStopTime Stop time of event
 * @param {string} eventFilter Filter of event (used for filtering in sidebar of calendar)
 */
function showEvents(eventTitle, eventDesc, eventStartTime, eventStopTime, eventFilter){
    var eventId = crypto.randomUUID();
    var weekday_index = eventStartTime.getDay();
    var timeCol = document.getElementById(`time-col-${weekday_index}`);

    let calendarDaysPlaceholder = document.getElementById("days");
    let calendarDays = calendarDaysPlaceholder.getElementsByTagName("li");

    // Get the displayed first date.
    var startDisplayedDate = selectedDate;
    if (Array.isArray(displayedDates)){
        startDisplayedDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() - selectedDate.getDay());
    }
    
    
}

function addEventToCalendar(columnid, eventTitle, eventDesc, eventStartTime, eventStopTime, eventFilter){
    var timeHeight = getCSSvariable('--time-height');
    var timePadding = getCSSvariable('--time-padding-top');

    // eventTop = `calc(calc(${eventStartTime.getHours()} * 60 + ${eventStartTime.getMinutes()}) / calc(${timeHeight} * 24 * 60) )`;
    timeDiff = (eventStopTime - eventStartTime)/1000/60;
    // eventHeight = `calc(${timeDiff} / calc(${timeHeight} * 24 * 60) )`;

    eventTop = `calc(${timeHeight} * calc(calc(${eventStartTime.getHours()} * 60 + ${eventStartTime.getMinutes()}) / 60))`;
    eventHeight = `calc(${timeHeight} * ${timeDiff} / 60)`;

    var column = document.getElementById(`time-col-${columnid}`);
    column.innerHTML += `<div class='event' style='top:${eventTop}; height: ${eventHeight}'>${eventTitle}</div>`
}

function clearView(){
    var timetable = document.getElementById("time-row");
    var timeColumns = [...timetable.getElementsByClassName("time-column")];
    timeColumns.forEach(col => {
        col.innerHTML = "";
    });
}

function generateTimeCols(numCols){
    var timetable = document.getElementById("time-row");
    if (timetable.innerHTML){
        timetable.innerHTML = "";
    }
    for (let i = 0; i < numCols; i++){
        timetable.innerHTML += `<div class='time-column' id='time-col-${i}'></div>`;
    }
}

function toggleView(){
    var viewSelector = document.getElementById("view-selector");
    if (viewSelector.innerHTML.includes("Week")){
        viewSelector.innerHTML = "Day";
        generateTimeCols(1);
        clickDate(`curr_${selectedDate.getDate()}`);
        return;
    }
    if (viewSelector.innerHTML.includes("Day")){
        viewSelector.innerHTML = "Week";
        generateTimeCols(7);
        clickDate(`curr_${selectedDate.getDate()}`);
        return;
    }
}

function toggleSidebar(){
    var sidebar = document.getElementById("sidebar");
    var calendar = document.getElementById('weekly-calendar');
    var sidebar_menu = document.getElementById('sidebar-menu');
    if (sidebar.classList.contains('hidden')){
        sidebar.classList.remove('hidden');
        calendar.classList.remove('full-size');
        sidebar_menu.innerHTML = `<img src='../icons/icons8-close-win.svg'>`
        return;
    }
    else {
        sidebar.classList.add('hidden');
        calendar.classList.add('full-size');
        sidebar_menu.innerHTML = `<img src='../icons/icons8-menu-win.svg'>`
        return;
    }
}