<template>
    <!-- prettier-ignore -->
    <app-layout>
        <update-received ref="dlg6" v-model="invItem" />
        <confirm ref="dlg1" @confirmed="rejectInv2()">
            <jet-label for="type" value="Select rejection reason:" />
            <select id="type" v-model="cancelReason" class="mt-1 block w-full">
                <option value="Wrong invoice details">Wrong invoice details</option>
            </select>
        </confirm>
        <div class="py-4">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <Table :resource="items">
                        <template #cell(status)="{ item: item }">
                            {{ __(item.status) }}
                        </template>
                        <template #cell(statusReason)="{ item: item }">
                            {{ __(item.statusReason) }}
                        </template>
                        <template #cell(dateTimeIssued)="{ item: item }">
                            {{ new Date(item.dateTimeIssued).toLocaleDateString() }}
                        </template>
                        <template #cell(dateTimeReceived)="{ item: item }">
                            {{ new Date(item.dateTimeReceived).toLocaleDateString() }}
                        </template>
                        <template #cell(actions)="{ item: item }">
                            <jet-danger-button class="me-2 mt-2" @click="rejectInvoice(item)"
                                v-show="item.status == 'Valid'">
                                {{ __("Reject") }}
                            </jet-danger-button>
                            <jet-button class="me-2 mt-2" @click="updateReceived(item)" v-show="item.status == 'Valid'">
                                {{ __("Direction") }}
                            </jet-button>

                            <secondary-button class="me-2 mt-2" v-show="item.status == 'Valid'" @click="openExternal(item)">
                                {{ __("ETA1") }}
                            </secondary-button>

                            <jet-button class="me-2 mt-2" v-show="item.status == 'Valid'" @click="openExternal2(item)">
                                {{ __("ETA2") }}
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
import AddEditItem from "@/Pages/Items/AddEdit.vue";
import Confirm from "@/UI/Confirm.vue";
import JetLabel from "@/Jetstream/Label.vue";
import PreviewInvoice from "@/Pages/Invoices/Preview.vue";
import UpdateReceived from "@/Pages/Invoices/UpdateReceived.vue";
import CreditNote from "@/Pages/Invoices/CreditNote.vue";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from '@/Jetstream/DangerButton.vue';
import Dropdown from "@/Jetstream/Dropdown.vue";
import swal from "sweetalert";

export default {
    components: {
        Dropdown,
        AppLayout,
        Confirm,
        PreviewInvoice,
        UpdateReceived,
        CreditNote,
        JetLabel,
        Table,
        JetButton,
        JetDangerButton,
        AddEditItem,
        SecondaryButton,
    },
    props: {
        items: Object,
    },
    data() {
        return {
            invItem: { quantity: 1009 },
            cancelReason: "",
            notSortableCols: [
                "statusReason",
                "receiver.name",
                "receiver.receiver_id",
                "issuerName",
                "receiverId",
                "receiverName",
            ],
        };
    },
    methods: {
        openExternal2(item) {
            window.open(route('eta.invoice.download', { uuid: item.uuid }), '_blank');
        },
        openExternal(item) {
            window.open(
                this.$page.props.preview_url +
                item.uuid +
                "/share/" +
                item.longId
            );
        },
        updateReceived(item) {
            this.invItem = item;
            this.$nextTick(() => {
                this.$refs.dlg6.ShowDialog();
            });
        },
        rejectInvoice(item) {
            this.invItem = item;
            this.$refs.dlg1.ShowModal();
        },
        rejectInv2() {
            axios
                .post(route("eta.invoices.cancel"), {
                    uuid: this.invItem.uuid,
                    status: "rejected",
                    reason: this.cancelReason,
                })
                .then((response) => {
                    alert(response.data);
                })
                .catch((error) => {
                    alert(error.response.data);
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
</style>
