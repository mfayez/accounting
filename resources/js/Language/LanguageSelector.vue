<template>
    <div class="mx-2 md:mx-4 mb-3 lg:mb-0">
        <inertia-link
            :href="route('language', [selectable_locale])"
            :class="classes"
            :onFinish="changeDocumentDirection"
        >
            <i class="fa fa-globe ltr:mr-2 rtl:ml-2"></i>
            <div v-if="selectable_locale == 'ar'">AR</div>
            <div v-else>EN</div>
        </inertia-link>
    </div>
</template>

<script>
export default {
    methods: {
        changeDocumentDirection() {
            document.querySelector("html").dir =
                this.$page.props.locale == "ar" ? "rtl" : "ltr";
        },
    },
    computed: {
        selectable_locale() {
            if (this.$page.props.locale == "ar") {
                return "en";
            }
            return "ar";
        },
        classes() {
            return "inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-black hover:text-red-700 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out";
        },
    },
};
</script>
