<template>
    <modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div>
            <div class="text-lg px-6 py-3 relative bg-[#4099de] text-white">
                <slot name="title"> </slot>
                <button
                    class="self-center px-5 py-1 absolute top-0 rtl:left-0 ltr:right-0 bottom-0"
                    @click="close"
                >
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <hr class="mb-5" />
            <div class="mt-4 px-6 pb-4">
                <slot name="content"> </slot>
            </div>
        </div>
        <hr class="mt-5" />
        <div class="px-6 py-4 text-right">
            <slot name="footer"> </slot>
        </div>
    </modal>
</template>

<script>
import Modal from "./Modal.vue";

export default {
    emits: ["close"],

    components: {
        Modal,
    },

    props: {
        show: {
            default: false,
        },
        maxWidth: {
            default: "2xl",
        },
        closeable: {
            default: true,
        },
    },

    methods: {
        close() {
            this.$emit("close");
        },
    },
};
</script>
