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
                    <Table
                        :filters="queryBuilderProps.filters"
                        :search="queryBuilderProps.search"
                        :columns="queryBuilderProps.columns"
                        :on-update="setQueryBuilder"
                        :meta="branches"
                    >
                        <template #head>
                            <tr>
                                <th
                                    v-show="showColumn('Id')"
                                    @click.prevent="sortBy('Id')"
                                >
                                    {{ __("ID") }}
                                </th>
                                <th
                                    v-show="showColumn('name')"
                                    @click.prevent="sortBy('name')"
                                >
                                    {{ __("Name") }}
                                </th>
                                <th
                                    v-show="showColumn('receiver_id')"
                                    @click.prevent="sortBy('receiver_id')"
                                >
                                    {{ __("Registration Number") }}
                                </th>

                                <th
                                    v-show="showColumn('type')"
                                    @click.prevent="sortBy('type')"
                                >
                                    {{ __("Type(B|P)") }}
                                </th>
                                <th style="text-align:center"   >
                                    {{ __("Branch Logo") }}
                                </th>
                                <th @click.prevent="">{{ __("Actions") }}</th>
                            </tr>
                        </template>

                        <template #body>
                            <tr
                                v-for="branch in branches.data"
                                :key="branch.id"
                            >
                                <td v-show="showColumn('Id')">
                                    {{ branch.Id }}
                                </td>
                                <td v-show="showColumn('name')">
                                    {{ branch.name }}
                                </td>
                                <td v-show="showColumn('receiver_id')">
                                    {{ branch.receiver_id }}
                                </td>
                                <td v-show="showColumn('type')">
                                    {{
                                        branch.type == "B"
                                            ? __("Business")
                                            : __("Individual")
                                    }}
                                </td>
                                <td v-if="Object.keys(images).length > 0">
                                    <template v-if="images[branch.Id] != 'N/A'">
                                        <img
                                            :src="
                                                '/storage/' +
                                                images[branch.Id]
                                            "
                                            alt="Branch Image"
                                            class="object-cover"
                                        />
                                    </template>
                                    <span v-else>{{ __("No Image") }}</span>
                                </td>
                                <td>
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
                                </td>
                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Confirm from "@/UI/Confirm";
import EditBranch from "@/Pages/Branches/Edit";
import {
    InteractsWithQueryBuilder,
    Tailwind2,
} from "@protonemedia/inertiajs-tables-laravel-query-builder";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import axios from "axios";

export default {
    mixins: [InteractsWithQueryBuilder],
    components: {
        AppLayout,
        Confirm,
        EditBranch,
        Table: Tailwind2.Table,
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
