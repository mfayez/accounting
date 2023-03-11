<template>
    <app-layout>
        <edit-branch ref="dlg2" :branch="branch" />
        <confirm ref="dlg1" @confirmed="remove()">
            {{ __("Are you sure you want to delete this branch?") }}
        </confirm>
        <div class="py-4">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4"
                >
                    <Table :resource="branches" >
                        <template #cell(type)="{ item: branch }">
                            {{
                                branch.type == "B"
                                    ? __("Business")
                                    : branch.type == "P"
                                    ? __("Person")
                                    : __("Foreign Customer")
                            }}
                        </template>
                        <template #cell(logo)="{ item: branch }">
                            <template v-if="images[branch.Id] != 'N/A'">
                                <img :src="'/storage/' + images[branch.Id]" alt="Branch Image" class="object-cover"/>
                            </template>
                            <span v-else>{{ __("No Image") }}</span>
                        </template>
                        <template #cell(actions)="{ item: branch }">
                            <secondary-button
                                @click="editBranch(branch)"
                            >
                                <i class="fa fa-edit"></i>
                                {{ __("Edit") }}
                            </secondary-button>
                            <jet-button
                                class="ms-2"
                                @click="removeBranch(branch)"
                            >
                                <i class="fa fa-trash"></i>
                                {{ __("Delete") }}
                            </jet-button>
                        </template>
                    </Table>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from "@/Layouts/AppLayout.vue";
    import Confirm from "@/UI/Confirm.vue";
    import EditBranch from "@/Pages/Branches/Edit.vue";
    import {
        Table
    } from "@protonemedia/inertiajs-tables-laravel-query-builder";
    import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
    import JetButton from "@/Jetstream/Button.vue";
    import axios from "axios";

export default {
    components: {
        AppLayout,
        Confirm,
        EditBranch,
        Table,
        SecondaryButton,
        JetButton,
    },
    props: {
        branches: Object,
    },
    data() {
        return {
            branch: Object,
            images: Object,
        };
    },
    methods: {
        editBranch(cust) {
            this.branch = cust;
            this.$nextTick(() => this.$refs.dlg2.ShowDialog());
            //this.$refs.dlg2.ShowDialog();
        },
        removeBranch(cust) {
            this.branch = cust;
            this.$refs.dlg1.ShowModal();
        },
        remove() {
            axios
                .delete(route("branches.destroy", { branch: this.branch.Id }))
                .then((response) => {
                    this.$store.dispatch("setSuccessFlashMessage", true);
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                })
                .catch((error) => {
                    //this.$refs.password.focus()
                });
        },
        getImages() {
            const ids = this.branches.data.map((branch) => branch.Id).join(",");

            axios
                .get(`/getBranchesImages/${ids}`)
                .then((res) => {
                    this.images = res.data;
                })
                .catch((err) => console.error(err));
        },
    },
    mounted() {
        this.getImages();
    },
};
</script>
<style scoped>
:deep(table th) {
    text-align: start;
}
img {
    max-width: 50%;
    max-height: 100%;
    margin: auto;
}
</style>
