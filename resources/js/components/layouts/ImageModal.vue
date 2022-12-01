<template>
    <div class="modal fade image-modal" id="image-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <img class="full-size-image" :src="props.image">
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
const props = defineProps({
    image: {
        required: true,
        type: String,
        default: null
    }
});
</script>