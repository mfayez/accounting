<template>
    <!-- prettier-ignore -->
    <app-layout>
		<preview-invoice ref="dlg3" v-model="invItem" />
        <credit-note ref="dlg5" v-model="invItem" />
        <debit-note ref="dlg6" v-model="invItem" />
        <confirm ref="dlg2" @confirmed="cancelInv2()">
			<jet-label for="type"  :value="__('Select cancelation reason')" />
			<select id="type" v-model="cancelReason" class="mt-1 block w-full">
			    <option :value="__('Wrong buyer details')">
                    {{__("Wrong buyer details")}}
                </option>
			    <option :value="__('Wrong invoice details')">
                    {{__("Wrong invoice details")}}
                </option>
			</select>
		</confirm>
		<confirm ref="dlg4" @confirmed="deleteInv()">
			<jet-label for="type"  value="Are you sure you want to delete this Invoice?" />
		</confirm>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
					<Table
						:filters="queryBuilderProps.filters"
						:search="queryBuilderProps.search"
						:columns="queryBuilderProps.columns"
						:on-update="setQueryBuilder"
						:meta="items"
				  	>
						<template #head>
						  	<tr>
                                <th>
                                    <input type="checkbox" v-model="allChecked" v-on:change="checkAll()"/>
                                </th>
                                <template v-for="(col, key) in queryBuilderProps.columns" :key="key">
                                    <th v-show="showColumn(key)" 
                                        v-if="notSortableCols.includes(key)">{{ col.label }}</th>
                                    <th class="cursor-pointer" v-show="showColumn(key)" @click.prevent="sortBy(key)" v-else>{{ col.label }}</th>
                                </template>
								<!-- <th 
									v-for="(col, key) in queryBuilderProps.columns" 
									:key="key" v-show="showColumn(key)" 
                                    @click.prevent="sortBy(key)"
								>
									{{ col.label }}
								</th> -->
                                <th>
                                    <dropdown
                                        :align="alignDropDown()"
                                        width="48"
                                        class="ms-3 mb-3 lg:mb-0"
                                    >
                                        <template #trigger>
                                            <jet-button>
                                                {{ __("Bulk Actions") }}
                                            </jet-button>
                                            
                                        </template>
                                        <template #content>
                                            <dropdown-link
                                                as="a"
                                                @click.prevent="ApproveSelected()"
                                                href="#"
                                            >
                                                {{ __("Approve Selected") }}
                                            </dropdown-link>
                                        </template>
                                    </dropdown>
                                </th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="item in items.data" :key="item.id" 
                                :class="{ credit: item.documentType =='C', debit: item.documentType =='D' }"
                            >
                                <td>
                                    <input type="checkbox" v-model="item.selected" :value="item.id" />
                                </td>
                                <td v-for="(col, key) in queryBuilderProps.columns" :key="key" v-show="showColumn(key)">
                                    <div v-for="rowVals in nestedIndex(item, key).split(',')">
                                        {{ 
                                            key == 'status' ? render_status(item, rowVals) :
                                            (key == 'statusReason' ? __(rowVals) :
                                            (key == 'dateTimeIssued' || key == 'dateTimeReceived' ? 
                                                new Date(rowVals).toISOString().slice(0,10) : 
                                                rowVals
                                            )) 
                                        }}
                                    </div>
                                </td>
                                <td>
                                    <div class="grid grid-cols-3 w-56">
                                        <jet-danger-button
                                            class="me-2 mt-2"
                                            @click="cancelInvoice(item)" 
                                            v-show="item.status=='Valid'"
                                            >
                                            {{ __("Cancel") }}
                                        </jet-danger-button>
                                        
                                        <jet-danger-button
                                            class="me-2 mt-2"
                                            @click="deleteInvoice(item)" 
                                            v-show="item.status!='Valid' && item.status!='approved' && item.status!='Submitted'"
                                            >
                                            {{ __("Delete") }}
                                        </jet-danger-button>
                                        
                                        <jet-button
                                        class="me-2 mt-2"
                                            @click="editInvoice(item)" 
                                            v-show="item.status=='In Review'"
                                            >
                                            {{ __("Edit") }}
                                        </jet-button>
                                        
                                        <secondary-button
                                            class="me-2 mt-2"
                                            @click="viewInvoice(item)"
                                        >
                                            {{ __("View") }}
                                        </secondary-button>

                                        <jet-button
                                            class="me-2 mt-2"
                                            @click="ApproveItem(item)"
                                            v-show="item.status=='In Review'"
                                        >
                                            {{ __("Approve") }}
                                        </jet-button>
                                        
                                        <jet-button
                                            class="me-2 mt-2"
                                            @click="downloadPDF(item)" 
                                        >
                                            {{ __("PDF") }}
                                        </jet-button>
                                        
                                        <secondary-button
                                            class="me-2 mt-2"
                                            v-show="item.status=='Valid'"
                                            @click="openExternal(item)"
                                        >
                                            {{ __("ETA1") }}
                                        </secondary-button>
                                        
                                        <jet-button
                                            class="me-2 mt-2"
                                            v-show="item.status=='Valid'"
                                            @click="openExternal2(item)">
                                            {{ __("ETA2") }}
                                        </jet-button>

                                        <secondary-button
                                            class="me-2 mt-2"
                                            v-show="item.status=='Valid'"
                                            @click="creditNoteUpdate(item)"
                                        >
                                            {{ __("Credit") }}
                                        </secondary-button>

                                        <jet-button
                                            class="me-2 mt-2"
                                            v-show="item.status=='Valid'"
                                            @click="debitNoteUpdate(item)"
                                        >
                                            {{ __("Debit") }}
                                        </jet-button>
                                    </div>
                                    
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
import {
    InteractsWithQueryBuilder,
    Tailwind2,
} from "@protonemedia/inertiajs-tables-laravel-query-builder";
import AddEditItem from "@/Pages/Items/AddEdit";
import Confirm from "@/UI/Confirm";
import JetLabel from "@/Jetstream/Label";
import PreviewInvoice from "@/Pages/Invoices/Preview";
import UpdateReceived from "@/Pages/Invoices/UpdateReceived";
import CreditNote from "@/Pages/Invoices/CreditNote";
import DebitNote from "@/Pages/Invoices/DebitNote";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from '@/Jetstream/DangerButton';
import Dropdown from "@/Jetstream/Dropdown";
import swal from "sweetalert";
import DropdownLink from "@/Jetstream/DropdownLink";

export default {
    mixins: [InteractsWithQueryBuilder],
    components: {
        Dropdown, DropdownLink,
        AppLayout,
        Confirm,
        PreviewInvoice,
        UpdateReceived,
        CreditNote,
        DebitNote,
        JetLabel,
        Table: Tailwind2.Table,
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
            window.open(route('eta.invoice.download', {uuid: item.uuid}), '_blank');
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
                    row.selected = this.allChecked;
                });
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
        render_status: function(item, status) {
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
            } catch (err) {}
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
