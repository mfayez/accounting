<template>
    <app-layout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <archives-table v-if="results.length" :data="results" />
                    <div>
                        <pre>{{results}}</pre>
                        <pre>{{metadata}}</pre>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import ArchivesTable from "@/Pages/Archives/ArchivesTable.vue";
import axios from "axios";

export default {
    components: {
        ArchivesTable,
        AppLayout,
        SecondaryButton,
        JetButton,
    },
    props: {
        results: Object,
        metadata: Object
    },
    data() {
        return {
            branch: Object,
            images: Object,
        };
    },
    methods: {
        //todo
        getPackage() {
            //todo
        },
    },
    mounted() {
        //todo
    },
};
</script>
<style scoped>
:deep(table th) {
    text-align: start;
}
</style>
