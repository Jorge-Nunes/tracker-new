<script setup>
import { computed, defineProps, defineEmits, watch } from 'vue';

const props = defineProps({
    items: Array,
    perPage: Number || String,
    modelValue: Number,
    onlyProccess: {
        type: Boolean,
        default: false,
    },
    showPagination: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['update:modelValue', 'changeList']);

const page = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit('update:modelValue', value);
    },
});

const list = computed(() => {
    const currentInit = page.value * props.perPage - props.perPage;
    const parsed = props.items.slice(currentInit, page.value * props.perPage);
    emit('changeList', parsed);
    return parsed;
});

watch(list, (value) => {
    if (props.onlyProccess) {
        emit('changeList', value);
    }
});

const totalPages = computed(() => Math.ceil(props.items.length / props.perPage));
const nextPages = computed(() => {
    console.log(`next`);
    let pages = [];

    if (page.value > 3) {
        if (page.value + 3 >= totalPages.value) {
            pages.push(totalPages.value - 3, totalPages.value - 2, totalPages.value - 1);
        } else {
            let maxPage = page.value + 2 < totalPages.value ? page.value + 2 : totalPages.value - 2;

            let pageValue = page.value;
            if (page.value === 1) {
                maxPage++;
                pageValue++;
            }
            for (let i = pageValue - 1; i < maxPage; i++) {
                pages.push(i);
            }
        }
    } else {
        if (totalPages.value < 5) {
            for (let i = 2; i <= totalPages.value; i++) {
                pages.push(i);
            }
        } else {
            pages = [2, 3, 4, 5];
        }
    }
    return pages;
});
</script>

<template>
    <div v-for="(item, index) in list" :key="`item-${index}`">
        <slot :item="item" :index="index"></slot>
    </div>

    <div v-if="showPagination" class="paginate-component">
        <button class="first-end-btn first" :disabled="page === 1" @click="page--">&lt;</button>

        <div class="pagination-body">
            <button :class="{ 'selected-page': page === 1 }" @click="page = 1">1</button>
            <span v-if="page > 3">...</span>

            <button
                :class="{ 'selected-page': page === currentPage }"
                @click="page = currentPage"
                v-for="currentPage in nextPages"
                :key="currentPage">
                {{ currentPage }}
            </button>

            <span v-if="page < totalPages - 3">...</span>
            <button
                v-if="totalPages > 5"
                :class="{ 'selected-page': page === totalPages }"
                @click="page = totalPages"
                >{{ totalPages }}</button
            >
        </div>

        <button class="first-end-btn" :disabled="page === totalPages" @click="page++">&gt;</button>
    </div>
</template>

<style>
.paginate-component {
    width: 100%;
    display: flex;
    justify-content: center;
    text-align: center;
    margin-top: 1rem;
    
    & .first-end-btn {
        font-size: 0.8rem !important;
        background: var(--headers-bg);
        color: var(--headers-text);
        padding: 0.3rem 0.7rem;
        font-weight: bolder;
        border-radius: 50%;
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    & .first-end-btn.first {
        justify-content: flex-start;
    }

    & .first-end-btn[disabled] {
        opacity: 0.5;
    }

    & .pagination-body {
        font-size: 0.8rem !important;
        background: var(--headers-bg);
        color: var(--headers-text);
        padding: 0.2rem 0.7rem;
        font-weight: bolder;
        text-align: center;
        border-radius: 2rem;
        display: flex;
        justify-content: center;
    }

    & .selected-page {
        background: var(--selection) !important;
        color: var(--selection-text) !important;
        font-weight: bolder;
    }

    & button {
        font-size: 0.8rem !important;
        border: 0;
        background: var(--headers-bg);
        color: var(--headers-text);
        padding: 0.3rem 0.3rem;
        border-radius: 50%;
        margin: 0 2px;
        width: 2rem;
        height: 2rem;
    }

    & span {
        padding: 0.3rem;
        margin: 0 2px;
    }
}
</style>