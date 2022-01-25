<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-[#4099de] border border-white font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#4099de] active:bg-gray-900 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-80 disabled:opacity-25 transition"
        v-bind:class="[isRounded ? 'rounded-md' : '']"
    >
        <slot></slot>
    </button>
</template>

<script>
export default {
    props: {
        isRounded: {
            type: Boolean,
            default: true,
        },
        type: {
            type: String,
            default: "submit",
        },
    },
};
</script>
