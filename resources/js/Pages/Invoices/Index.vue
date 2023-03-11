<template>
    <app-layout>
        <preview-invoice ref="dlg3" v-model="invItem" />
        <credit-note ref="dlg5" v-model="invItem" />
        <debit-note ref="dlg6" v-model="invItem" />
        <confirm ref="dlg2" @confirmed="cancelInv2()">
            <jet-label for="type" :value="__('Select cancelation reason')" />
            <select id="type" v-model="cancelReason" class="mt-1 block w-full">
                <option :value="__('Wrong buyer details')">
                    {{ __("Wrong buyer details") }}
                </option>
                <option :value="__('Wrong invoice details')">
                    {{ __("Wrong invoice details") }}
                </option>
            </select>
        </confirm>
        <confirm ref="dlg4" @confirmed="deleteInv()">
            <jet-label for="type" value="Are you sure you want to delete this Invoice?" />
        </confirm>
        <div class="py-4">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <Table :resource="items">
                        <template #tableReset>
                            <jet-button class="me-2" @click="checkAll()">
                                {{ allChecked ? __("Unselect All") : __("Select All") }}
                            </jet-button>
                            <dropdown :align="alignDropDown()" width="48" class="ms-3 lg:mb-0">
                                <template #trigger>
                                    <jet-button>
                                        {{ __("Bulk Actions") }}
                                    </jet-button>
                                </template>
                                <template #content>
                                    <dropdown-link as="a" @click.prevent="ApproveSelected()" href="#">
                                        {{ __("Approve Selected") }}
                                    </dropdown-link>
                                </template>
                            </dropdown>
                        </template>
                        <template #cell(select)="{ item: invoice }">
                            <input type="checkbox" v-model="invoice.selected" :value="invoice.id" />
                        </template>
                        <template #cell(actions)="{ item: invoice }">
                            <jet-danger-button class="me-2 mt-2" @click="cancelInvoice(invoice)"
                                v-show="invoice.status == 'Valid'">
                                {{ __("Cancel") }}
                            </jet-danger-button>

                            <jet-danger-button class="me-2 mt-2" @click="deleteInvoice(invoice)"
                                v-show="invoice.status != 'Valid' && invoice.status != 'approved' && invoice.status != 'Submitted'">
                                {{ __("Delete") }}
                            </jet-danger-button>

                            <jet-button class="me-2 mt-2" @click="editInvoice(invoice)"
                                v-show="['in review', 'invalid', 'approved', 'rejected', 'cancelled'].indexOf(invoice.status.toLowerCase()) >= 0">
                                {{ __("Edit") }}
                            </jet-button>

                            <secondary-button class="me-2 mt-2" @click="viewInvoice(invoice)">
                                {{ __("View") }}
                            </secondary-button>

                            <jet-button class="me-2 mt-2" @click="ApproveItem(invoice)"
                                v-show="invoice.status == 'In Review'">
                                {{ __("Approve") }}
                            </jet-button>

                            <jet-button class="me-2 mt-2" @click="downloadPDF(invoice)">
                                {{ __("PDF") }}
                            </jet-button>

                            <secondary-button class="me-2 mt-2" v-show="invoice.status == 'Valid'"
                                @click="openExternal(invoice)">
                                {{ __("ETA1") }}
                            </secondary-button>

                            <jet-button class="me-2 mt-2" v-show="invoice.status == 'Valid'"
                                @click="openExternal2(invoice)">
                                {{ __("ETA2") }}
                            </jet-button>

                            <secondary-button class="me-2 mt-2" v-show="invoice.status == 'Valid'"
                                @click="creditNoteUpdate(invoice)">
                                {{ __("Credit") }}
                            </secondary-button>

                            <jet-button class="me-2 mt-2" v-show="invoice.status == 'Valid'"
                                @click="debitNoteUpdate(invoice)">
                                {{ __("Debit") }}
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
import DebitNote from "@/Pages/Invoices/DebitNote.vue";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from '@/Jetstream/DangerButton.vue';
import Dropdown from "@/Jetstream/Dropdown.vue";
import swal from "sweetalert";
import DropdownLink from "@/Jetstream/DropdownLink.vue";
import axios from 'axios';

export default {
    components: {
        Dropdown, DropdownLink,
        AppLayout,
        Confirm,
        PreviewInvoice,
        UpdateReceived,
        CreditNote,
        DebitNote,
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
            allChecked: false,
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
        downloadPDF(item) {
            this.invItem = item;
            window.open(route("pdf.invoice.preview", [item.Id]));
        },
        editInvoice(item) {
            this.invItem = item;
            window.location.href = route("invoices.edit", [item.Id]);
        },
        viewInvoice(item) {
            this.invItem = item;
            this.$nextTick(() => {
                this.$refs.dlg3.ShowDialog();
            });
        },
        ApproveItem(item) {
            swal({
                title: this.__("Are you sure?"),
                text: this.__("Once approved it will be sent to ETA"),
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willApprove) => {
                    if (willApprove) {
                        axios
                            .post(route("eta.invoices.approve"), {
                                Id: item.Id,
                            })
                            .then((response) => {
                                swal(this.__("Invoice has been approved!"), {
                                    icon: "success",
                                }).then(() => {
                                    location.reload();
                                });
                            })
                            .catch((error) => {
                                swal(error.response.data, {
                                    icon: "error",
                                });
                            });

                    }
                });
        },
        creditNoteUpdate(item) {
            this.invItem = item;
            this.$nextTick(() => {
                this.$refs.dlg5.ShowDialog();
            });
        },
        debitNoteUpdate(item) {
            this.invItem = item;
            this.$nextTick(() => {
                this.$refs.dlg6.ShowDialog();
            });
        },
        deleteInvoice(item) {
            this.invItem = item;
            this.$refs.dlg4.ShowModal();
        },
        deleteInv() {
            axios
                .post(route("eta.invoices.delete"), { Id: this.invItem.Id })
                .then((response) => {
                    location.reload();
                })
                .catch((error) => {
                    alert(error.response.data);
                });
        },
        cancelInvoice(item) {
            this.invItem = item;
            this.$refs.dlg2.ShowModal();
        },
        cancelInv2() {
            axios
                .post(route("eta.invoices.cancel"), {
                    uuid: this.invItem.uuid,
                    status: "cancelled",
                    reason: this.cancelReason,
                })
                .then((response) => {
                    console.log(response);
                    alert(response.data);
                    //location.reload();
                })
                .catch((error) => {
                    console.log(error);
                    alert(error.response.data);
                    //this.$refs.password.focus()
                });
        },
        checkAll() {
            this.$nextTick(() => {
                this.items.data.forEach((row) => {
                    row.selected = !this.allChecked;
                });
                this.allChecked = !this.allChecked;
            });
        },
        ApproveSelected() {
            var temp = this.items.data.filter((row) => row.selected)
                .map((row) => row.Id);
            if (temp.length == 0) {
                swal(this.__("Please select at least one invoice!"), {
                    icon: "error",
                });
                return;
            }
            swal({
                title: this.__("Are you sure?"),
                text: this.__("Once approved it will be sent to ETA"),
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willApprove) => {
                    if (willApprove) {
                        axios
                            .post(route("eta.invoices.approve"), {
                                Id: this.items.data
                                    .filter((row) => row.selected)
                                    .map((row) => row.Id),
                            })
                            .then((response) => {
                                swal(this.__("Invoices has been approved!"), {
                                    icon: "success",
                                }).then(() => {
                                    location.reload();
                                });
                            })
                            .catch((error) => {
                                swal(error.response.data, {
                                    icon: "error",
                                });
                            });

                    }
                });
        },
        alignDropDown() {
            return this.$page.props.locale == "en" ? "left" : "right";
        },
        render_status: function (item, status) {
            if (item.cancelRequestDate && item.status != 'Cancelled')
                return this.__("Cancel Pending");
            if (item.rejectRequestDate && item.status != 'Rejected')
                return this.__("Reject Pending");
            return this.__(status);
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
