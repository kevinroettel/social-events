<template>
    <div v-if="artist != null">
        <div class="card w-50 mx-auto">
            <div class="card-body">
                <h3 class="text-center">{{ artist.name }}</h3>
                <a v-if="artist.website != null" :href="artist.website">Zur Webseite des Künstlers</a>
                <hr>

                <ArtistTags
                    :artistTags="artist.tags"
                    @tag-added="addTagToArtist($event)"
                    @tag-removed="removeTagFromArtist($event)"
                />

                <hr>
                <div class="card text-center">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a 
                                    :class="`nav-link ${show == 'upcoming' ? 'active' : ''}`"
                                    @click="show = 'upcoming'"
                                >
                                    Bevorstehende Veranstaltungen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a 
                                    :class="`nav-link ${show == 'past' ? 'active' : ''}`"
                                    @click="show = 'past'"
                                >
                                    Vergangene Veranstaltungen
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div 
                        v-if="show == 'upcoming'"
                        class="card-body"
                    >
                        <EventList 
                            :events="upcomingEvents" 
                            :parentModel="'artist'"
                            @show-event-page="showEventPage($event)"
                        />
                    </div>
                    <div 
                        v-else-if="show == 'past'"
                        class="card-body"
                    >
                        <EventList 
                            :events="pastEvents" 
                            :parentModel="'artist'"
                            @show-event-page="showEventPage($event)"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import EventList from '../layouts/EventList.vue';
import ArtistTags from '../layouts/ArtistTags.vue';
import { ref, inject, onMounted } from 'vue';
import { useArtistStore } from '../../stores/ArtistStore.js';
import { useEventStore } from '../../stores/EventStore.js';
import { useLocationStore } from '../../stores/LocationStore.js';
import { toast } from '../helpers/toast';

const artistStore = useArtistStore();
const eventStore = useEventStore();
const locationStore = useLocationStore();

const axios = inject('axios');

const props = defineProps({
    artistId: {
        required: true,
        type: Number,
        default: null,
    }
});

const emit = defineEmits([
    'show-event-page'
]);

const show = ref('upcoming');

const artist = ref(null);
const upcomingEvents = ref(null);
const pastEvents = ref(null)

const getArtistData = () => {
    artist.value = artistStore.getArtistById(props.artistId);
    upcomingEvents.value = eventStore.getEventsWithArtist(props.artistId);
    upcomingEvents.value.forEach((event, index) => {
        if (!event.hasOwnProperty('location')) {
            upcomingEvents.value[index].location = locationStore.getLocationById(event.location_id);
        }
    });

    pastEvents.value = eventStore.getPastEventsWithArtist(props.artistId);
}

const showEventPage = (eventId) => emit('show-event-page', eventId);

const addTagToArtist = (tag) => {
    axios.patch(
        `/artists/${artist.value.id}/tags/${tag.id}`
    ).then((response) => {
        artist.value.tags.push(tag);
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

const removeTagFromArtist = (tagId) => {
    axios.delete(
        `/artists/${artist.value.id}/tags/${tagId}`
    ).then((response) => {
        artist.value.tags.forEach((tag, index) => {
            if (tag.id == tagId) {
                artist.value.tags.splice(index, 1);
                return;
            }
        });
    }).catch((error) => {
        toast(error.message, 'error');
    })
}

onMounted(() => {
    if (props.artistId != null) getArtistData();
})
</script>