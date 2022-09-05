<template>
    <!-- prettier-ignore -->
    <app-layout>
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
                                               (key == 'start_date' || key == 'end_date' ? 
                                                    new Date(rowVals).toLocaleDateString() : 
                                                    rowVals
                                               ) 
                                            }}
                                        </div>
									</td>
                                        <div class="grid grid-cols-3 w-56">
                                            <jet-button 
                                                class="me-2 mt-2"
                                                @click="downloadZip(item)" 
                                                v-show="item.status=='Ready'"
                                            >
                                                {{ __("ZIP") }}
                                            </jet-button>
                                        </div>
                                    <td>
                                        
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
        downloadZip(item) {
            this.invItem = item;
            window.open(route("archive.download", [item.id]));
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
            return this.__(key);
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
