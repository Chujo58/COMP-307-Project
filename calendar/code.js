let date = new Date(),
currYear = date.getFullYear(),
currMonth = date.getMonth();

const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

function renderCalender(){
    let firstDay = new Date(currYear, currMonth, 1).getDay(),
    lastDate = new Date(currYear, currMonth+1, 0).getDate(),
    lastDay = new Date(currYear, currMonth, lastDate).getDay()
    lastDateLastMonth = new Date(currYear, currMonth, 0).getDate();

    let liTags = "";
    for (let i = firstDay; i > 0; i--){
        liTags += `<li class='inactive'>${lastDateLastMonth- i + 1}</li>`;
    }

    for (let i = 1; i <= lastDate; i++){
        let isToday = i === date.getDate() && currMonth === new Date().getMonth() && currYear === new Date().getFullYear() ? "active" : "";

        liTags += `<li class='${isToday}'>${i}</li>`
    }

    for (let i = lastDay; i < 6; i++){
        liTags += `<li class='inactive'>${i - lastDay + 1}</li>`;
    }

    // currentDate.innerHTML = `${months[currMonth]} ${currYear}`;
    document.getElementById("current-month").innerHTML = `${months[currMonth]} ${currYear}`;
    document.getElementById("days").innerHTML = liTags;
}

window.addEventListener('load', renderCalender);