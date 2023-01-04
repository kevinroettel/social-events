<template>
    <div class="accordion" id="posts-accordion">
        <div  
            v-for="(post, index) in posts"
            :key="index"
            class="accordion-item"
        >
            <h2 class="accordion-header" :id="'post-heading-' + index">
                <button 
                    class="accordion-button" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    :data-bs-target="'#post-collapse-' + index" 
                    aria-expanded="false" 
                    :aria-controls="'#post-collapse-' + index"
                >
                    <PostContent
                        :post="post"
                    />
                </button>
            </h2>
            <div 
                :id="'post-collapse-' + index" 
                class="accordion-collapse collapse" 
                :aria-labelledby="'post-heading-' + index" 
                data-bs-parent="#posts-accordion"
            >
                <div class="accordion-body display-grid">
                    <PostContent
                        v-for="(comment) in post.comments"
                        :key="comment.id"
                        :post="comment"
                    />

                    <CreatePost
                        :eventId="post.event_id"
                        :answerTo="post.id"
                        @created-post="addPost($event)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import PostContent from './PostContent.vue';
import CreatePost from './CreatePost.vue';
import { onMounted, reactive, ref } from '@vue/runtime-core';

const props = defineProps({
    postsData: {
        required: true,
        type: Array,
        default: null
    }
});

const posts = ref(null);

const addPost = (newPost) => {
    posts.value.push(newPost);
}

onMounted(() => {
    posts.value = props.postsData;
})
</script>