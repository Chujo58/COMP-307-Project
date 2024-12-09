let date = new Date(),
currYear = date.getFullYear(),
currMonth = date.getMonth();

let selectedDate = date;

const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
const short_months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

function getWeek(array, id){
    for (let i = 0; i < array.length; i+=7){
        for (let j = 0; j < 7; j++){
            if (array[i + j].id == id){
                return i;
            } 
        }    
    }
}

function clickDate(id){
    let calendarDaysPlaceholder = document.getElementById("days");
    let calendarDays = calendarDaysPlaceholder.getElementsByTagName("li");

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
    var index = 0;

    var contains_other_month = false;
    var before = false;
    currentWeekDays.forEach(day => {
        var labels = document.getElementById('weekly-calendar-label').getElementsByTagName("li");
        if (day.classList.contains('active')){
            labels[index].className = 'active';
        }
        else if (day.classList.contains('selected')){
            labels[index].className = 'selected';
        }
        else {
            labels[index].className = '';
        }
        var copy = day.cloneNode(true);
        if (copy.classList.contains('inactive')){
            copy.classList.remove('inactive');
            contains_other_month = true;
            before = index == 0;
        }
        weekDays.appendChild(copy);
        index++;
    });

    var day = id.split('_')[1];
    selectedDate = new Date(currYear, currMonth, day);
    var str = "";
    if (contains_other_month){
        var month = before ? currMonth - 1 : currMonth + 1;
        var year = month < 0 || month > 11 ? new Date(currYear, month).getFullYear() : currYear;
        var month = month < 0 || month > 11 ? new Date(currYear, month).getMonth() : month;

        str = before ? `${short_months[month]} ${year} - ${short_months[currMonth]} ${currYear}` : `${short_months[currMonth]} ${currYear} - ${short_months[month]} ${year}`;
    }
    else {
        str = `${months[currMonth]} ${currYear}`;
    }
    document.getElementById("current-week").innerHTML = str;
}

function renderCalender(){
    let firstDay = new Date(currYear, currMonth, 1).getDay(),
    lastDate = new Date(currYear, currMonth+1, 0).getDate(),
    lastDay = new Date(currYear, currMonth, lastDate).getDay()
    lastDateLastMonth = new Date(currYear, currMonth, 0).getDate();

    let liTags = "";
    for (let i = firstDay; i > 0; i--){
        liTags += `<li class='inactive' id='prev_${lastDateLastMonth- i + 1}' onclick='clickDate(this.id)'>${lastDateLastMonth- i + 1}</li>`;
    }

    for (let i = 1; i <= lastDate; i++){
        let isToday = i === date.getDate() && currMonth === new Date().getMonth() && currYear === new Date().getFullYear() ? "active" : "";

        liTags += `<li class='${isToday}' id='curr_${i}' onclick='clickDate(this.id)'>${i}</li>`
    }

    for (let i = lastDay; i < 6; i++){
        liTags += `<li class='inactive' id='next_${i - lastDay + 1}' onclick='clickDate(this.id)'>${i - lastDay + 1}</li>`;
    }

    document.getElementById("current-month").innerHTML = `${months[currMonth]} ${currYear}`;
    document.getElementById("days").innerHTML = liTags;
}

function toToday(){
    date = new Date();
    currMonth = date.getMonth();
    currYear = date.getFullYear();
    renderCalender();
    clickDate(`curr_${date.getDate()}`);
}

function renderTimes(){
    var times = document.getElementById("timestamps");
    times.innerHTML += `<li style='color:var(--calendar-color);'>.</li>`;
    for (let i = 1; i <= 12; i++){
        times.innerHTML += `<li>${i} AM</li>`;
    }
    for (let i = 1; i < 12; i++){
        times.innerHTML += `<li>${i} PM</li>`
    }
}

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
                selectedDate = new Date(currYear, currMonth, newDay);
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

