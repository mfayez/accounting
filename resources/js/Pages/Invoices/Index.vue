<template>
    <!-- prettier-ignore -->
    <app-layout>
		<preview-invoice ref="dlg3" v-model="invItem" />
		<confirm ref="dlg1" @confirmed="rejectInv2()">
			<jet-label for="type"  value="Select rejection reason:" />
			<select id="type" v-model="cancelReason" class="mt-1 block w-full">
				  <option value="Wrong invoice details">Wrong invoice details</option>
			</select>
		</confirm>
		<confirm ref="dlg2" @confirmed="cancelInv2()">
			<jet-label for="type"  value="Select cancelation reason:" />
			<select id="type" v-model="cancelReason" class="mt-1 block w-full">
			  <option value="Wrong buyer details">Wrong buyer details</option>
			  <option value="Wrong invoice details">Wrong invoice details</option>
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
								<th @click.prevent="">{{__('Actions')}}</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="item in items.data" :key="item.id">
									<td v-for="(col, key) in queryBuilderProps.columns" :key="key" v-show="showColumn(key)">
										<div v-for="rowVals in nestedIndex(item, key).split(',')">
											{{ key == 'status' || key == 'statusReason' ? __(rowVals) : rowVals }}</div>
									</td>
									<td>
                                        <div class="grid grid-cols-3 w-56">
                                            <jet-danger-button
                                                class="me-2 mt-2"
                                                @click="rejectInvoice(item)" 
                                                v-show="route().current('eta.invoices.received.index') && item.status =='Valid'"
                                                >
                                                {{ __("Reject") }}
                                            </jet-danger-button>
                                            
                                            <jet-danger-button
                                                class="me-2 mt-2"
                                                @click="cancelInvoice(item)" 
                                                v-show="route().current('eta.invoices.sent.index') && item.status=='Valid'"
                                                >
                                                {{ __("Cancel") }}
                                            </jet-danger-button>
                                            
                                            <jet-danger-button
                                                class="me-2 mt-2"
                                                @click="deleteInvoice(item)" 
                                                v-show="route().current('eta.invoices.sent.index') && item.status!='Valid' && item.status!='processing' && item.status!='approved'" 
                                                >
                                                {{ __("Delete") }}
                                            </jet-danger-button>
                                            
                                            <jet-button
                                            class="me-2 mt-2"
                                                @click="editInvoice(item)" 
                                                v-show="route().current('eta.invoices.sent.index') && item.status!='Valid'"
                                                >
                                                {{ __("Edit") }}
                                            </jet-button>
                                            
                                            <secondary-button
                                                class="me-2 mt-2"
                                                @click="viewInvoice(item)"
                                                v-show="route().current('eta.invoices.sent.index') && item.status=='Valid'"
                                            >
                                                {{ __("View") }}
                                            </secondary-button>
                                            
                                            <jet-button
                                                class="me-2 mt-2"
                                                @click="downloadPDF(item)" 
                                                v-show="route().current('eta.invoices.sent.index') && item.status=='Valid'"
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
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from '@/Jetstream/DangerButton';
import Dropdown from "@/Jetstream/Dropdown";

export default {
    mixins: [InteractsWithQueryBuilder],
    components: {
        Dropdown,
        AppLayout,
        Confirm,
        PreviewInvoice,
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
:deep(table th) {
    text-align: start;
}
</style>
