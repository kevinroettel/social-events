<template>
    <div class="card mb-3">
        <div class="card-header">
            Vorschläge basierend auf ihren Events
        </div>
        <div class="card-body">
            <div v-if="events.length == 0">
                <p>Zurzeit können wir dir keine Events basierend auf ihren Präferenzen anzeigen.</p>
            </div>

            <EventTeaserCarousel
                v-else
                :events="events"
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

const interests = ref([]);

const events = ref([]);

const getData = () => {
    let tagsFromEvents = [];

    tagsFromEvents.push(...getTagsFromArtists(eventStore.getWatchlist));
    tagsFromEvents.push(...getTagsFromArtists(eventStore.getOldWatchlist));
    
    let tags = countTags(tagsFromEvents);
    
    
    console.log(tags)
}

const getTagsFromArtists = (watchlistArray) => {
    let tags = [];
    
    watchlistArray.forEach(entry => {
        entry.event.artists.forEach(artist => {
            if (Number.isInteger(artist)) {
                let artistTags = artistStore.getArtistById(artist).tags.map(tag => tag.name);
                tags.push(...artistTags);
            } else {
                tags.push(...artist.tags.map(tag => tag.name))
            }
        })
    });

    return tags;
}

const countTags = (tagArray) => {
    let tags = [];

    tagArray.forEach((tagFromEvent, index) => {
        if (tags.length == 0) {
            tags.push({'name': tagFromEvent, 'count': 1});
        } else {
            let isNotInTags = true;

            tags.forEach((tag, index) => {
                if (tag.name == tagFromEvent) {
                    tags[index].count++;
                    isNotInTags = false;
                    return;
                }
            })

            if (isNotInTags) {
                tags.push({'name': tagFromEvent, 'count': 1});
            }
        }
    })

    return tags;
}

onMounted(() => {
    getData()
})
</script>