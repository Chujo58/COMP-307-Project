let date = new Date(),
currYear = date.getFullYear(),
currMonth = date.getMonth();

let selectedDate = date;

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
    var labels = document.getElementById('weekly-calendar-label').getElementsByTagName("li");
    
    for (let i = 0; i < 7; i++){
        var day = currentWeekDays[i];
        if (day.classList.contains('active')){
            labels[i].className = 'active';
        }
        else if (day.classList.contains('selected')){
            labels[i].className = 'selected';
        }
        else {
            labels[i].className = '';
        }
        var copy = day.cloneNode(true);
        if (copy.classList.contains('inactive')){
            copy.classList.remove('inactive');
            contains_other_month = true;
            before = !before ? i == 0 : before;
        }
        weekDays.appendChild(copy);
    }

    // Updates the month title
    updateMonthTitle(id, contains_other_month, before);
}


function updateMonthTitle(id, contains_other_month, before)
{
    var day = id.split('_')[1];
    var monthChange = 0;
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
    selectedDate = new Date(currYear, currMonth + monthChange, day);
    var str = "";
    if (contains_other_month)
    {
        var month = before ? currMonth - 1 : currMonth + 1;
        var year = month < 0 || month > 11 ? new Date(currYear, month).getFullYear() : currYear;
        var month = month < 0 || month > 11 ? new Date(currYear, month).getMonth() : month;

        str = before ? `${short_months[month]} ${year} - ${short_months[currMonth]} ${currYear}` : `${short_months[currMonth]} ${currYear} - ${short_months[month]} ${year}`;
    }
    else
    {
        str = `${months[currMonth]} ${currYear}`;
    }
    document.getElementById("current-week").innerHTML = str;
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
                numDays = icon.id === "prev_week" ? -7 : 7;
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

/**
 * Function to create events in the calendar.
 * @param {string} eventTitle Name of the event
 * @param {string} eventDesc Description of the event
 * @param {Date} eventStartTime Start time of event
 * @param {Date} eventStopTime Stop time of event
 * @param {string} eventFilter Filter of event (used for filtering in sidebar of calendar)
 */
function addEvent(eventTitle, eventDesc, eventStartTime, eventStopTime, eventFilter){
    var eventId = crypto.randomUUID();
    var weekday_index = eventStartTime.getDay();
    var timeCol = document.getElementById(`time-col-${weekday_index}`);

    let calendarDaysPlaceholder = document.getElementById("days");
    let calendarDays = calendarDaysPlaceholder.getElementsByTagName("li");

    var week_id = getWeek(calendarDays, id);    
    var currentWeekDays = [...calendarDays].slice(week_id,week_id+7);
}

// TODO: Finish implementing the clearView method
function clearView(){
    var timetable = document.getElementById("time-row");
    var timeColumns = timetable.getElementsByClassName("time-column");
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
        return;
    }
    if (viewSelector.innerHTML.includes("Day")){
        viewSelector.innerHTML = "Week";
        generateTimeCols(7);
        return;
    }
}