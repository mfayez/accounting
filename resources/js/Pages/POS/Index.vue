<template>
    <app-layout>
        <edit-pos ref="dlg2" :positem="pos_item" />
        <confirm ref="dlg1" @confirmed="remove()">
            {{ __("Are you sure you want to delete this pos?") }}
        </confirm>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4"
                >
                    <Table
                        :filters="queryBuilderProps.filters"
                        :search="queryBuilderProps.search"
                        :columns="queryBuilderProps.columns"
                        :on-update="setQueryBuilder"
                        :meta="poses"
                    >
                        <template #head>
                            <tr>
                                <th
                                    v-show="showColumn('id')"
                                    @click.prevent="sortBy('id')"
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
                                    v-show="showColumn('serial')"
                                    @click.prevent="sortBy('serial')"
                                >
                                    {{ __("serial") }}
                                </th>
                                <th
                                    v-show="showColumn('grant_type')"
                                    @click.prevent="sortBy('grant_type')"
                                >
                                    {{ __("Grant Type") }}
                                </th>
                                

                                <th @click.prevent="">{{ __("Actions") }}</th>
                            </tr>
                        </template>

                        <template #body>
                            <tr
                                v-for="pos_item in poses.data"
                                :key="pos_item.id"
                            >
                                <td v-show="showColumn('id')">
                                    {{ pos_item.id }}
                                </td>
                                <td v-show="showColumn('name')">
                                    {{ pos_item.name }}
                                </td>
                                <td v-show="showColumn('serial')">
                                    {{ pos_item.serial }}
                                </td>
                                <td v-show="showColumn('grant_type')">
                                    {{ pos_item.grant_type }}
                                </td>
                               
                                <td>
                                    <secondary-button
                                        @click="editPOS(pos_item)"
                                    >
                                        <i class="fa fa-edit"></i>
                                        {{ __("Edit") }}
                                    </secondary-button>
                                    <jet-button
                                        class="ms-2"
                                        @click="removePOS(pos_item)"
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
import EditPos from "@/Pages/POS/Add";
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
        EditPos,
        Table: Tailwind2.Table,
        SecondaryButton,
        JetButton,
    },
    props: {
        poses: Object,
    },
    data() {
        return {
            pos_item: Object,
        };
    },
    methods: {
        editPOS(p_pos) {
            this.pos_item = p_pos;
            this.$nextTick(() => this.$refs.dlg2.ShowDialog());
            //this.$refs.dlg2.ShowDialog();
        },
        removePOS(p_pos) {
            this.pos_item = p_pos;
            this.$refs.dlg1.ShowModal();
        },
        remove() {
            axios
                .delete(route("pos.destroy", this.pos_item.id))
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
    },
    mounted() {
        
    },
};
</script>
<style scoped>
:deep(table th) {
    text-align: start;
}
</style>
