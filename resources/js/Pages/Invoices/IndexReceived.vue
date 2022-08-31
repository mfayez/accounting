<template>
    <!-- prettier-ignore -->
    <app-layout>
		<update-received ref="dlg6" v-model="invItem" />
        <confirm ref="dlg1" @confirmed="rejectInv2()">
			<jet-label for="type"  value="Select rejection reason:" />
			<select id="type" v-model="cancelReason" class="mt-1 block w-full">
				  <option value="Wrong invoice details">Wrong invoice details</option>
			</select>
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
								<th @click.prevent="">{{__('Actions')}}</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="item in items.data" :key="item.id" 
                                :class="{ credit: item.typeName =='c' }"
                            >
									<td v-for="(col, key) in queryBuilderProps.columns" :key="key" v-show="showColumn(key)">
										<div v-for="rowVals in nestedIndex(item, key).split(',')">
											{{ 
                                                key == 'status' || key == 'statusReason' ? __(rowVals) :
                                               (key == 'dateTimeIssued' || key == 'dateTimeReceived' ? 
                                                    new Date(rowVals).toLocaleDateString() : 
                                                    rowVals
                                               ) 
                                            }}
                                        </div>
									</td>
									<td>
                                        <div class="grid grid-cols-3 w-56">
                                            <jet-danger-button
                                                class="me-2 mt-2"
                                                @click="rejectInvoice(item)" 
                                                v-show="item.status =='Valid'"
                                                >
                                                {{ __("Reject") }}
                                            </jet-danger-button>
                                            <jet-button
                                                class="me-2 mt-2"
                                                @click="updateReceived(item)" 
                                                v-show="item.status =='Valid'"
                                                >
                                                {{ __("Direction") }}
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
import UpdateReceived from "@/Pages/Invoices/UpdateReceived";
import CreditNote from "@/Pages/Invoices/CreditNote";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from '@/Jetstream/DangerButton';
import Dropdown from "@/Jetstream/Dropdown";
import swal from "sweetalert";

export default {
    mixins: [InteractsWithQueryBuilder],
    components: {
        Dropdown,
        AppLayout,
        Confirm,
        PreviewInvoice,
        UpdateReceived,
        CreditNote,
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
            } catch (err) {}
            return "N/A";
        },
    },
};
</script>
<style scoped>
:deep(table th) {
    text-align: start;
}
.credit {
    background-color: lightgoldenrodyellow;
}
</style>
