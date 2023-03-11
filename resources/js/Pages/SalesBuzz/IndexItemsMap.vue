<template>
    <!-- prettier-ignore -->
    <app-layout>
        <edit-item-map ref="dlg2" :item_map="currentItem" />
        <confirm ref="dlg4" @confirmed="deleteItemMap2()">
            <jet-label for="type" :value="__('Are you sure you want to delete this item?')" />
        </confirm>
        <div class="py-4">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <Table :resource="items">
                        <template #cell(actions)="{ item: item }">
                            <jet-danger-button class="me-2 mt-2" @click="deleteItemMap(item)">
                                {{ __("Delete") }}
                            </jet-danger-button>

                            <jet-button class="me-2 mt-2" @click="editItemMap(item)">
                                {{ __("Edit") }}
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
import {
    Table,
} from "@protonemedia/inertiajs-tables-laravel-query-builder";
import Confirm from "@/UI/Confirm.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from '@/Jetstream/DangerButton.vue';
import Dropdown from "@/Jetstream/Dropdown.vue";
import EditItemMap from "@/Pages/SalesBuzz/EditItemMap.vue";
import swal from "sweetalert";

export default {
    components: {
        Dropdown,
        AppLayout,
        Confirm,
        JetLabel,
        Table,
        JetButton,
        JetDangerButton,
        SecondaryButton,
        EditItemMap,
    },
    props: {
        items: Object,
    },
    data() {
        return {
            currentItem: {},

            notSortableCols: [
            ],
        };
    },
    methods: {
        editItemMap(item) {
            this.currentItem = item;
            this.$nextTick(() => this.$refs.dlg2.ShowDialog());
        },
        deleteItemMap(item) {
            this.currentItem = item;
            this.$refs.dlg4.ShowModal();
        },
        deleteItemMap2() {
            axios
                .post(route("sb.items.map.delete"), { SBCode: this.currentItem.SBCode })
                .then((response) => {
                    this.$store.dispatch("setSuccessFlashMessage", true);
                    this.showDialog = false;
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                })
                .catch((error) => {
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors; //.password[0];
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
            } catch (err) { }
            return "N/A";
        },
        editItem: function (item_id) {
            //alert(JSON.stringify(item_id));
        },
    },
};
</script>
<style scoped>
:deep(table td) {
    text-align: start;
    white-space: pre-line;
}

:deep(table th) {
    text-align: start;
    white-space: pre-line;
}

.credit {
    background-color: lightgoldenrodyellow;
}

.debit {
    background-color: palegreen;
}
</style>
