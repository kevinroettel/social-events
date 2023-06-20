<template>
    <div>
        <h1>Kalender</h1>

        <h3 id="month-and-year">{{ months[current.month] }} {{ current.year }}</h3>
        <table id="calender" class="w-100" style="height: 50vh;">
            <thead>
                <tr>
                    <th v-for="day in ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So']">{{ day }}</th>
                </tr>
            </thead>
            <tbody id="calender-body" class="table-bordered" :set="calDate = 1">
                <tr v-for="i in 6" :key="i">
                    <template v-for="j in 7" :key="j">
                        <td v-if="i === 1 && j < getFirstDay()"></td>
                        <td v-else-if="calDate > daysInMonth()"></td>
                        <td v-else :class="getCurrentDayClass(calDate)">
                            <span class="calender-date border-0">{{ calDate++ }}</span>
                            <span :id="buildDayId(calDate)" class="border-0">

                            </span>
                        </td>
                    </template>
                </tr>
            </tbody>
        </table>

        <button id="previous-month" @click="previousMonth()">Zurück</button>
        <button id="next-month" @click="nextMonth()">Weiter</button>
    </div>
</template>
<script setup>
import { onBeforeUpdate, onMounted, onUpdated, reactive, ref, watch } from 'vue';
import { useEventStore } from '../../stores/EventStore';
import { useRouter } from 'vue-router';

const router = useRouter();
const eventStore = useEventStore();

const today = new Date();

const calenderEvents = ref([]);

const current = reactive({
    month: today.getMonth(),
    year: today.getFullYear(),
});

const months = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];

const daysInMonth = () => {
    return 32 - new Date(current.year, current.month, 32).getDate();
}

const getFirstDay = () => {
    return (new Date(current.year, current.month)).getDay();
}

const previousMonth = () => {
    current.year = (current.month === 0) ? current.year - 1 : current.year;
    current.month = (current.month === 0) ? 11 : current.month - 1;
}

const nextMonth = () => {
    current.year = (current.month === 11) ? current.year + 1 : current.year;
    current.month = (current.month + 1) % 12;
}

const buildDayId = (date) => {
    return `calender-${current.year}-${("0" + (current.month + 1)).slice(-2)}-${("0" + (date - 1)).slice(-2)}`;
}

const getCurrentDayClass = (date) => {
    if (date === today.getDate() && 
        current.year === today.getFullYear() && 
        current.month === today.getMonth()) 
        return "bg-light"
}

const fillCalender = () => {
    eventStore.getWatchlist.forEach((entry) => {
        createEventElement(
            entry.event.id, entry.event.date, entry.event.name, entry.event.location.name, 
            entry.status == 'attending' ? 'primary' : 'info'
        );
    });

    eventStore.getAllEvents.forEach((event) => {
        createEventElement(event.id, event.date, event.name, event.location.name, 'white');
    });

    // todo: add friend watchlist entries
}

const createEventElement = (id, date, name, location, color) => {
    let calenderCell = document.getElementById(`calender-${date}`);
    if (calenderCell == null) return

    console.log(`calender-${date}`, calenderCell)
    let eventsParagraph = document.createElement("p");
    eventsParagraph.setAttribute('id', `calender-event-${id}`)
    eventsParagraph.classList.add("bg-" + color);
    calenderEvents.value.push(`calender-event-${id}`);
    let textNode = document.createTextNode(`${name}, ${location}`)
    eventsParagraph.appendChild(textNode)
    eventsParagraph.addEventListener("click", function() { router.push(`/event/${id}`) })
    calenderCell.appendChild(eventsParagraph)
}

const destroyAllEventElements = () => {
    calenderEvents.value.forEach((paragraph) => {
        let el = document.getElementById(paragraph)
        if (el != null) el.remove();
    })

    calenderEvents.value = []
}

onBeforeUpdate(() => {
    destroyAllEventElements()
})

onUpdated(() => {
    fillCalender()
})

onMounted(() => {
    fillCalender();
})
</script>
