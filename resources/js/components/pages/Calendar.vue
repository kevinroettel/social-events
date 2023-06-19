<template>
    <div>
        <h1>Kalender</h1>

        <h3 id="month-and-year">{{ months[current.month] }} {{ current.year }}</h3>
        <table id="calender">
            <thead>
                <tr>
                    <th v-for="day in ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']">{{ day }}</th>
                </tr>
            </thead>
            <tbody id="calender-body">
                <tr v-for="i in 7" :key="i">
                    <td v-for="j in 7" v-if="calDate < daysInMonth(current.month, current.year)">
                        <span v-if="i === 1 && j < getFirstDay()">none</span>
                        <span v-else>{{ calDate++ }}</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- <button id="previous-month" @click="previousMonth()">Zurück</button>
        <button id="next-month" @click="nextMonth()">Weiter</button> -->
    </div>
</template>
<script setup>
import { onMounted, reactive, ref } from 'vue';


const today = new Date();

const current = reactive({
    month: today.getMonth(),
    year: today.getFullYear(),
});

const calDate = ref(1);

const months = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];

const daysInMonth = () => {
    return 32 - new Date(current.year, current.month, 32).getDate();
}

const getFirstDay = () => {
    return (new Date(current.year, current.month)).getDay();
}

// const previousMonth = () => {
//     current.year = (current.month === 0) ? current.year - 1 : current.year;
//     current.month = (current.month === 0) ? 11 : current.month - 1;
//     showCalendar(current.month, current.year);
// }

// const nextMonth = () => {
//     current.year = (current.month === 11) ? current.year + 1 : current.year;
//     current.month = (current.month + 1) % 12;
//     showCalendar(current.month, current.year);
// }



const showCalendar = (month, year) => {
    let tableBody = document.getElementById('calender-body');
    tableBody.innerHTML = "";

    document.getElementById('month-and-year').innerHTML = months[month] + " " + year;

    let date = 1;
    for (let i = 1; i < 7; i++) {
        let row = document.createElement("tr");

        for (let j = 1; j <= 7; j++) {
            if (i === 1 && j < getFirstDay()) {
                let cell = document.createElement("td");
                let cellText = document.createTextNode("");
                cell.appendChild(cellText);
                row.appendChild(cell);
            } else if (date > daysInMonth(month, year)) {
                console.log(date, daysInMonth(month, year))
                break;
            } else {
                let cell = document.createElement("td");
                let cellText = document.createTextNode(date);
                if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                    cell.classList.add("bg-info");
                }
                cell.appendChild(cellText);
                row.appendChild(cell);
                date++;
            }
        }

        tableBody.appendChild(row);
    }
}



onMounted(() => {
    // showCalendar(current.month, current.year);
})
</script>
