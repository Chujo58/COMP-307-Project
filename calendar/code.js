let date = new Date(),
currYear = date.getFullYear(),
currMonth = date.getMonth();

let selectedDate = date;
let displayedDates = null;
let loadedInMobile = false;

const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
const short_months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
const weekday_labels = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
const long_weekday_labels = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday","Friday","Saturday"];
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
        clearView();
        displayFiltered(true);
    }
    else if (numDays == 1){
        ({ contains_other_month, before } = generateDailyDays_Labels(numDays, contains_other_month, before, weekDays, id));
        displayedDates = document.getElementById(id);
        clearView();
        displayFiltered(false);
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
    clickDate(`curr_${date.getDate()}`);
}

/**
 * Renders the times on the side of the weekly view.
 */
function renderTimes(){
    var times = document.getElementById("timestamps");
    times.innerHTML += `<div><span>12 AM</span></div>`;
    for (let i = 1; i < 12; i++){
        times.innerHTML += `<div><span>${i} AM</span></div>`;
    }
    times.innerHTML += `<div><span>12 PM</span></div>`;
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
    if (loadedInMobile){
        return;
    }
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
                clearView();
                var viewSelectorText = document.getElementById("view-selector").innerHTML;
                var daysToAdd = 0;
                var weeklyViewToggled = false;
                if (viewSelectorText.includes('Week')) {
                    daysToAdd = 7;
                    weeklyViewToggled = true;
                }
                if (viewSelectorText.includes('Day')) {
                    daysToAdd = 1;
                }

                numDays = icon.id === "prev_week" ? -daysToAdd : daysToAdd;
                var newDay = selectedDate.getDate() + numDays;
                selectedDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), newDay);
                currMonth = selectedDate.getMonth();
                currYear = selectedDate.getFullYear();

                renderCalender();
                clickDate(`curr_${selectedDate.getDate()}`);
                displayFiltered(weeklyViewToggled);
            });
        }
    });
    renderCalender();
    loadCalendarIcons();
    renderTimes();
    loadFilters();
    showCreate();
    clickDate(`curr_${date.getDate()}`);
}

function showCreate(){
    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function(){
        if (this.readyState == 4 && this.status == 200){
            if (this.responseText !== "Staff"){
                document.getElementById('add_event_btn').style = 'display: none;';
                document.getElementById('calendar-add-placeholder').classList.add('hidden');
            }
            else {
                document.getElementById('add_event_btn').style = 'display: flex;';
                document.getElementById('calendar-add-placeholder').classList.remove('hidden');
            }
        }
    }

    xhttp.open("GET", "php/calendar.php");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();    
}

function loadCalendarIcons(){
    var current_day = date.getDate();
    var icon_path = "icons/calendar/Calendar " + current_day + ".png";

    document.getElementById('calendar-page-icon').innerHTML = `
        <span><img src='${icon_path}'></span>
        Calendar
    `;
}

function createBooking(){
    let empty = isFieldEmpty('event_name_book');
    empty = isFieldEmpty('event_start_book') || empty;
    empty = isFieldEmpty('event_stop_book') || empty;
    empty = isFieldEmpty('event_desc_book') || empty;
    empty = isFieldEmpty('event_filter_book') || empty;

    if (empty){
        return;
    }

    var name = getValue('event_name_book');
    var start = new Date(getValue('event_start_book'));
    var stop = new Date(getValue('event_stop_book'));
    var desc = getValue('event_desc_book');
    var filter = getValue('event_filter_book');

    var fname = getValue('fname_book');
    var lname = getValue('lname_book');
    var email = getValue('email_book');

    var eventid = window.location.href.split("event_id=")[1];
    var event = document.getElementById(eventid);
    
    var elem = document.getElementById('event-booking-message');
    if (start >= new Date(Number(event.getAttribute('event_start'))) && stop <= new Date(Number(event.getAttribute('event_stop')))){
        var type = 'booking';
    } else {
        var type = 'pending';
        if (start.getDate() != stop.getDate()){
            elem.style.color = 'red';
            elem.innerHTML = "Cannot create event. Dates aren't in same day.";
            return;
        }

        if (start >= stop){
            elem.style.color = 'red'
            elem.innerHTML = "Cannot create event. End time after start time.";
            return;
        }
    
        if (start <= new Date()){
            elem.style.color = 'red';
            elem.innerHTML = "Cannot create event. Date before current date and time";
            return;
        }
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function (){
        if (this.readyState == 4){
            if (this.status == 200){
                if (this.responseText == 'Guest'){
                    var elems = document.getElementById('booking-form').querySelectorAll('input.hidden-form');
                    elems.forEach(element => {
                        element.classList.remove('hidden-form');
                    });
                }
                if (this.responseText == 'Created booking'){
                    elem.style.color = 'green';
                    elem.innerHTML = 'Booking created.';
                    setTimeout(function(){
                        document.getElementById('calendar').click();
                    }, 1500);
                } else {
                    elem.style.color = 'red';
                    elem.innerHTML = 'Need additional information';
                }
            } else {
                elem.style.color = 'red';
                elem.innerHTML = "An error occurred while processing the request.";
            }
        }
    }
    xhttp.open("POST", "php/show_event_details.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(`name=${name}&start=${start.getTime()}&stop=${stop.getTime()}&desc=${desc}&filter=${filter}&fname=${fname}&lname=${lname}&email=${email}&type=${type}&event_id=${eventid}`);
}

function addEvent(){
    let empty = isFieldEmpty('event_name');
    empty = isFieldEmpty('event_start') || empty;
    empty = isFieldEmpty('event_stop') || empty;
    empty = isFieldEmpty('event_desc') || empty;
    empty = isFieldEmpty('event_filter') || empty;

    if (empty){
        return;
    }

    var name = getValue('event_name');
    var start = new Date(getValue('event_start'));
    var stop = new Date(getValue('event_stop'));
    var desc = getValue('event_desc');
    var filter = getValue('event_filter');

    var elem = document.getElementById('calendar-create-error');
    if (start.getDate() != stop.getDate()){
        elem.style.color = 'red';
        elem.innerHTML = "Cannot create event. Dates aren't in same day.";
        return;
    }

    if (start >= stop){
        elem.style.color = 'red'
        elem.innerHTML = "Cannot create event. End time after start time.";
    }

    if (start <= new Date()){
        elem.style.color = 'red';
        elem.innerHTML = "Cannot create event. Date before current date and time";
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function (){
        if (this.readyState == 4 && this.status == 200){
            console.log(this.responseText);
            document.getElementById('calendar-create-form').reset();
            document.getElementById('calendar-popup').className='calendar-popup';
            setTimeout(function(){
                window.location.reload();
            }, 1500);
        }
    }
    xhttp.open("POST", "php/calendar.php");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(`name=${name}&start=${start.getTime()}&stop=${stop.getTime()}&desc=${desc}&filter=${filter}`);
}

function deleteEvent(id){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function (){
        if (this.readyState == 4 && this.status == 200){
            console.log(this.responseText);
            document.getElementById('calendar').click();
        }
    }
    
    xhttp.open("GET", `php/calendar.php?delete=true&event_id=${id}`);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}

/**
 * Shows all events of selected day.
 * @param {Date} day Day to show events
 * @param {boolean} weeklyView Is weekly view toggled on.
 */
function showEvents(day, weeklyView, filter, user, type){
    var weekday_index = weeklyView ? day.getDay() : 0;
    var start_timestamp = new Date(day.getFullYear(), day.getMonth(), day.getDate()).getTime();
    var stop_timestamp = new Date(day.getFullYear(), day.getMonth(), day.getDate()+1).getTime();
    
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function (){
        if (this.readyState == 4){
            if (this.status == 200){
                results = this.responseText.split('\\n');
                results = results.slice(0,results.length - 1);
                results.forEach(row => {
                    var data = row.split(',');
                    addEventToCalendar(weekday_index, data[0], data[1], data[2], data[3], data[4], data[5], data[6]);
                });
            }
            else {
                console.log("Request failed with status: " + this.status);
            }
        }
    }

    xhttp.open("GET", `php/calendar.php?start=${start_timestamp}&stop=${stop_timestamp}&filter=${filter}&user=${user}&type=${type}`, false);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}

function redirectToEvent(event_id){
    window.location.href = `index.php?Page=Event&event_id=${event_id}`;
}

/**
 * 
 * @param {Date} date 
 */
function formatDate(date){
    console.log(date);
    return long_weekday_labels.at(date.getDay()) + ', ' + months.at(date.getMonth()) + ' ' + date.getDate() + ' ' + date.getFullYear();
}

/**
 * 
 * @param {Date} start 
 * @param {Date} stop 
 */
function formatTimes(start, stop){
    var startstr = start.toTimeString().split(' ')[0];
    var stopstr = stop.toTimeString().split(' ')[0];
    return startstr.slice(0,startstr.length-3) + ' - ' + stopstr.slice(0,stopstr.length-3);
}

function popoutEvent(){
    var event = "";
    if (typeof eventID === 'undefined' || !eventID){
        event = "";
    } else {
        event = eventID;
    }
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200){
            var elem = document.getElementById('event-body');
            if (elem != null){
                results = this.responseText.split('\\n');
                results = results[0];
                results = results.split(',');
                var accounts_info = results[9] != 'availability' ? `
                    <div class='event_detail'>
                        <span><img src='icons/pulsar_line_account.png'></span>
                        ${results[7]}
                    </div>
                    <div class='event_detail'>
                        <span><img src='icons/pulsar_line_email.png'></span>
                        ${results[10]}
                    </div>
                ` : `
                    <div class='event_detail'>
                        <span><img src='icons/pulsar_line_teacher.png'></span>
                        ${results[6]}
                    </div>
                `;

                elem.innerHTML = `
                <div class='event_inner' id='${results[5]}' event_start='${results[2]}' event_stop='${results[3]}' event_type='${results[9]}'>
                    <span class='close_btn' onclick='window.history.back();'><img src='icons/pulsar_line_close.png'></span>
                    <div class='event_name'>
                    ${results[0]}
                    </div>
                    <div class='event_time_infos'>
                    <div class='event_date'>
                    ${formatDate(new Date(Number(results[2])))}
                        </div>
                        <span class='event_sep'>⋅</span>
                        <div class='event_time'>
                            ${formatTimes(new Date(Number(results[2])), new Date(Number(results[3])))}
                        </div>
                    </div>
                    <div class='event_details_holder'>
                        <div class='event_detail'>
                            <span><img src='icons/pulsar_line_multitext.png'></span>
                            ${results[1]}
                        </div>
                        ${accounts_info}
                        <div class='event_detail'>
                            <span><img src='icons/pulsar_line_calendar.png'></span>
                            ${results[4]}
                        </div>
                    </div>
                    <span class='delete_btn' style='visibility: ${results[8] == 'staff' ? 'visible' : 'hidden'}' onclick='deleteEvent("${results[5]}");'><img src='icons/pulsar_line_trash.png'></span>
                </div>`;

                clickDate(`curr_${new Date(Number(results[2])).getDate()}`);

                var form_holder = document.getElementById('booking-form-holder');
                if (results[9] == 'availability'){
                    form_holder.innerHTML = `
                        <form action="php/calendar.php" method="post" id="booking-form">
                            <div class="heading-highlight form-heading">
                                Create booking
                            </div>
                            <div id="event-booking-message"></div>
                            <input type="text" name="event_name" id="event_name_book" placeholder="Event Name" onfocusout="isFieldEmpty('event_name')">
                            <div></div>
                            <div class="form-label">Event Start Date</div>
                            <input type="datetime-local" name="event_start" id="event_start_book" onfocusout="isFieldEmpty('event_start')">
                            <div></div>
                            <div class="form-label">Event Stop Date</div>
                            <input type="datetime-local" name="event_stop" id="event_stop_book" onfocusout="isFieldEmpty('event_stop')">
                            <div></div>
                            <input type="text" name="event_desc" id="event_desc_book" placeholder="Description" onfocusout="isFieldEmpty('event_desc')">
                            <div></div>
                            <input type="text" name="event_filter" id="event_filter_book" placeholder="Course Name" onfocusout="isFieldEmpty('event_filter')">
                            <div></div>

                            <input type="text" class="hidden-form" name="fname" id="fname_book" placeholder="First Name" onfocusout="isFieldEmpty('fname')">
                            <div></div>
                            <input type="text" class="hidden-form" name="lname" id="lname_book" placeholder="Last Name" onfocusout="isFieldEmpty('lname')">
                            <div></div>
                            <input type="email" class="hidden-form" name="email" id="email_book" placeholder="Email" onfocusout="isFieldEmpty('email')">
                            <div></div>

                            <input type="button" value="Create" style="cursor: pointer;" onclick="createBooking();">
                        </form>
                    `;
                    form_holder.style.width = '35%';
                    document.getElementById('event-informations').style.width = '65%';
                } else {
                    form_holder.style.display = 'none';
                }
            }
        }
    }
    xhttp.open('GET', `php/show_event_details.php?event_id=${event}`, false);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send();
}

function addEventToCalendar(columnid, eventTitle, eventDesc, eventStartTimestamp, eventStopTimestamp, eventFilter, eventID, eventType){
    var timeHeight = getCSSvariable('--time-height');

    var eventStartTime = new Date(Number(eventStartTimestamp));
    var eventStopTime = new Date(Number(eventStopTimestamp));

    timeDiff = (eventStopTime - eventStartTime)/1000/60;
    eventTop = `calc(${timeHeight} * ${(eventStartTime.getHours() * 60 + eventStartTime.getMinutes())/ 60})`;
    eventHeight = `calc(${timeHeight} * ${timeDiff / 60})`;

    var column = document.getElementById(`time-col-${columnid}`);
    if (!column) {
        return;
    }
    // var redirect_data = eventType == 'availability' ? `redirectToEvent("${eventID}");` : '';
    var redirect_data = `redirectToEvent("${eventID}");`
    var eventPast = eventStopTime <= new Date();

    column.innerHTML += `<div class='event ${eventType} ${eventPast ? 'past-event' : ''}' style='top:${eventTop}; height: ${eventHeight}; cursor: ${eventPast ? 'not-allowed' : 'pointer'};' event_id='${eventID}' onclick='${eventPast ? '' : redirect_data}'><span>${eventTitle}</span></div>`
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

function displayEventForCurrView(weeklyView, filter, user, type){
    if (weeklyView){
        var i = 0;
        while (i < 7){
            new_date = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() - selectedDate.getDay() + i);
            showEvents(new_date, true, filter, user, type);
            i++;
        }
    } else {
        showEvents(selectedDate, false, filter, user, type);
    }
}

function toggleView(){
    var viewSelector = document.getElementById("view-selector");
    if (viewSelector.innerHTML.includes("Week")){
        viewSelector.innerHTML = "Day";
        generateTimeCols(1);
        clickDate(`curr_${selectedDate.getDate()}`);
        document.getElementById('time-col-0').style = 'width: 100%;'
        displayFiltered(false);
        return;
    }
    if (viewSelector.innerHTML.includes("Day")){
        viewSelector.innerHTML = "Week";
        generateTimeCols(7);
        clickDate(`curr_${selectedDate.getDate()}`);
        document.getElementById('time-col-0').style = '';
        displayFiltered(true);
        return;
    }
}

function toggleSidebar(){
    var sidebar = document.getElementById("sidebar");
    var calendar = document.getElementById('weekly-calendar');
    var sidebar_menu = document.getElementById('sidebar-menu');
    var add_event = document.getElementById('add_event_btn');

    if (sidebar.classList.contains('hidden')){
        sidebar.classList.remove('hidden');
        calendar.classList.remove('full-size');
        add_event.classList.remove('small');
        sidebar_menu.innerHTML = `<img src='icons/pulsar_line_close.svg'>`
        return;
    }
    else {
        sidebar.classList.add('hidden');
        calendar.classList.add('full-size');
        add_event.classList.add('small');
        sidebar_menu.innerHTML = `<img src='icons/pulsar_line_menu.svg'>`
        return;
    }
}

function loadFilters(){
    var user = "";
    if (typeof userID === 'undefined' || !userID){
        user = "";
    } else {
        user = userID;
    }
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if (this.readyState == 4 && this.status == 200){
            document.getElementById('filters').innerHTML = this.responseText;
        }
    };

    xhttp.open('GET', `php/calendar.php?loadFilters=true&user=${user}`, false);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}

function displayFiltered(weekly){
    var user = (typeof userID === 'undefined' || !userID) ? '' : userID;
    var type = (typeof eventType === 'undefined' || !eventType) ? '' : eventType;
    var filter = (typeof eventFilter === 'undefined' || !eventFilter) ? '' : eventFilter;
    
    var filters = document.querySelectorAll('div.filter');
    var clicked_filters = [];
    filters.forEach(filter => {
        var item = filter.getElementsByTagName('input')[0];
        if (item.checked){
            clicked_filters.push(item.getAttribute('event_filter'));
        }
    });

    clearView();
    if (filter) {
        displayEventForCurrView(weekly, filter, user, type);
        return;
    }
    clicked_filters.forEach(filter_id => {
        displayEventForCurrView(weekly, filter_id, user, type);       
    });
}

function changeFilter(){
    var viewSelector = document.getElementById("view-selector");
    if (viewSelector.innerHTML.includes("Week")){
        displayFiltered(true);
    }
    else {
        displayFiltered(false);
    }
}

function forceMobile(){
    toggleSidebar();
    document.getElementById('sidebar-menu').onclick = "";
    document.getElementById('sidebar-menu').style.cursor = 'not-allowed';
    toggleView();
    document.getElementById('view-selector').onclick = "";
    document.getElementById('view-selector').style.cursor = 'not-allowed';
}

function getUserNames(userID){
    var user = "";
    if (typeof userID === 'undefined' || !userID){
        user = "";
    } else {
        user = userID;
    }
    if (user == ''){
        return '';
    } else {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function(){
            if (this.readyState == 4 && this.status == 200){
                var results = this.responseText.split('\\n');
                results = results.slice(0, results.length - 1)[0];
                results = results.split(',');
                var toAdd = "";
                if (results[1].endsWith('s')){
                    toAdd = "' Calendar";
                } else {
                    toAdd = "'s Calendar";
                }
                document.getElementById('calendar_user_name').innerHTML = results[0] + ' ' + results[1] + toAdd;
            }
        };

        xhttp.open('GET', `php/calendar.php?get_name_id=${user}`, false);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send();
    }
}

function turnMobile(x, daily, sidebar_check=true){
    if (x.matches){
        onLoad();
        var sidebar = document.getElementById("sidebar");
        if (!sidebar.classList.contains('hidden') && sidebar_check){
            document.getElementById("sidebar-menu").click();
        }
        if (daily && document.getElementById('view-selector').innerHTML.includes('Week')){
            document.getElementById('view-selector').click();
        } else if (!daily && document.getElementById('view-selector').innerHTML.includes('Day')) {
            document.getElementById('view-selector').click();
        }
        loadedInMobile = true;
    }
}

var mediaUnder750 = window.matchMedia('screen and (max-device-width: 900px), screen and (max-width: 900px)');
var mediaUnder500 = window.matchMedia('screen and (max-device-width: 550px), screen and (max-width: 550px)');

mediaUnder750.addEventListener("change", function(){ turnMobile(mediaUnder750, false); })
mediaUnder500.addEventListener("change", function(){ turnMobile(mediaUnder500, true); })

window.addEventListener('load', function(){ 
    turnMobile(mediaUnder750, false); 
});
window.addEventListener('load', function(){ 
    turnMobile(mediaUnder500, true); 
});
