<template>
    <div class="card mb-3">
        <div class="card-header">
            Vorschläge basierend auf ihren Events (Cosine Similarity)
        </div>
        <div class="card-body">
            <div v-if="similarEvents.length == 0">
                <p>Zurzeit können wir dir keine Events basierend auf ihren Präferenzen anzeigen.</p>
            </div>

            <EventTeaserCarousel
                v-else
                :events="similarEvents"
            />
        </div>
    </div>
</template>
<script setup>
import EventTeaserCarousel from '../layouts/EventTeaserCarousel.vue';
import { useEventStore } from "../../stores/EventStore";
import { onMounted, ref } from 'vue';
import { useArtistStore } from '../../stores/ArtistStore';

const eventStore = useEventStore();
const artistStore = useArtistStore();

const similarEvents = ref([]);
const userEventVector = ref([]);


const getData = () => {
    let events = [];
    
    // muss getan werden, damit reactivity von vue verschwindet
    let entries = JSON.parse(JSON.stringify(eventStore.getWatchlist.concat(eventStore.getOldWatchlist)));

    if (entries.length == 0) return;

    entries.forEach(entry => {
        events.push({
            id: entry.event_id,
            artistsTags: entry.event.artists,
            average: []
        });
    });
    
    let longestAverage = 0;

    events.forEach((event, eIndex) => {
        let longestTagList = 0;

        event.artistsTags.forEach((artist, aIndex) => {
            if (Number.isInteger(artist)) {
                events[eIndex].artistsTags[aIndex] = artistStore.getArtistById(artist).tags.map(tag => tag.id)
            } else {
                events[eIndex].artistsTags[aIndex] = artist.tags.map(tag => tag.id);
            }

            if (longestTagList < events[eIndex].artistsTags[aIndex].length) 
                longestTagList = events[eIndex].artistsTags[aIndex].length;
        })

        for (let i = 0; i < longestTagList; i++) {
            let sum = 0;
            let count = 0;

            event.artistsTags.forEach(tags => {
                if (tags[i] != undefined) {
                    sum += tags[i];
                    count++;
                }
            });

            event.average[i] = sum / count;
        }

        if (longestAverage < event.average.length)
            longestAverage = event.average.length;
    })

    events = events.filter(event => event.average.length !== 0)

    for (let i = 0; i < longestAverage; i++) {
        let sum = 0;
        let count = 0;

        events.forEach((event) => {
            if (event.average[i] != undefined) {
                sum += event.average[i];
                count++;
            }
        });

        userEventVector.value[i] = sum / count;
    }

    getEvents();
}

const getEvents = () => {
    let events = JSON.parse(JSON.stringify(eventStore.getAllEvents));

    events.forEach((event, eIndex) => {
        let longestTagList = 0;

        event.artists.forEach((artist, aIndex) => {
            if (Number.isInteger(artist)) {
                events[eIndex].artists[aIndex] = artistStore.getArtistById(artist)
            }
            
            if (longestTagList < events[eIndex].artists[aIndex].tags.length)
                longestTagList = events[eIndex].artists[aIndex].tags.length;
        });

        events[eIndex].average = [];

        for (let i = 0; i < longestTagList; i++) {
            let sum = 0;
            let count = 0;

            event.artists.forEach(artist => {
                if (artist.tags[i] != undefined) {
                    sum += artist.tags[i].id;
                    count++;
                }
            });

            events[eIndex].average[i] = sum / count;
        }
    })

    findSimilarEvents(events)
}

const findSimilarEvents = (events) => {
    events.forEach((event, index) => {
        if (userEventVector.value.length == event.average.length) {
            let similarity = consineSimilarity(longer, shorter);

            console.log(similarity)

            if (similarity >= 0.8) {
                similarEvents.value.push(event);
            }
        }
    });
}

const consineSimilarity = (arrayA, arrayB) => {
    let dotProduct = 0;
    let mA = 0;
    let mB = 0;

    for (let i = 0; i < arrayA.length; i++) {
        dotProduct += (arrayA[i] * arrayB[i]);
        mA += (arrayA[i] * arrayA[i]);
        mB += (arrayB[i] * arrayB[i]);
    }

    mA = Math.sqrt(mA);
    mB = Math.sqrt(mB);
    let similarity = dotProduct / (mA * mB);

    return similarity;
}

onMounted(() => {
    getData()
})
</script>