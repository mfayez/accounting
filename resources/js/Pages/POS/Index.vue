<template>
    <app-layout>
        <edit-pos ref="dlg2" :positem="pos_item" />
        <confirm ref="dlg1" @confirmed="remove()">
            {{ __("Are you sure you want to delete this pos?") }}
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
                        :meta="poses"
                    >
                        <template #head>
                            <tr>
                                <template v-for="(col, key) in queryBuilderProps.columns" :key="key">
                                    <th v-show="showColumn(key)" 
                                        v-if="notSortableCols.includes(key)">{{ col.label }}</th>
                                    <th class="cursor-pointer" v-show="showColumn(key)"
                                        @click.prevent="sortBy(key)" v-else>{{ col.label }}</th>
                                  </template>
								<th @click.prevent="">{{__('Actions')}}</th>
							</tr>
                        </template>

                        <template #body>
                            <tr v-for="item in poses.data" :key="item.id">
                                <td v-for="(col, key) in queryBuilderProps.columns" :key="key" v-show="showColumn(key)">
                                    <div v-for="rowVals in nestedIndex(item, key).split(',')">
                                        {{rowVals}}
                                    </div>
                                </td>
                               
                                <td>
                                    <secondary-button
                                        @click="editPOS(item)"
                                    >
                                        <i class="fa fa-edit"></i>
                                        {{ __("Edit") }}
                                    </secondary-button>
                                    <jet-button
                                        class="ms-2"
                                        @click="removePOS(item)"
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
            notSortableCols: [
                "issuer.name",
                "grant_type"
            ],
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
        nestedIndex: function (item, key) {
            try {
                var keys = key.split(".");
                if (keys.length == 1) return item[key].toString();
                if (keys.length == 2) return item[keys[0]][keys[1]].toString();
                if (keys.length == 3)
                    return item[keys[0]][keys[1]][keys[2]].toString();
                return "Unsupported Nested Index";
            } catch (err) {}
            return "N/A";
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
