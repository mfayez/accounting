<template>
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
								<th v-for="(col, key) in queryBuilderProps.columns" :key="key" v-show="showColumn(key)">
									{{ col.label }}
								</th>
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
										<div>
										<button class="p-2 rounded-md bg-red-500 text-white hover:bg-red-600 mx-2" @click="rejectInvoice(item)"
											v-show="route().current('eta.invoices.received.index') && item.status =='Valid'"
										>
											<div>{{__('Reject')}}</div>
										</button>	
										<button class="p-2 rounded-md bg-red-500 text-white hover:bg-red-600 mx-2" @click="cancelInvoice(item)"
											v-show="route().current('eta.invoices.sent.index') && item.status=='Valid'"
										>
											<div>{{__('Cancel')}}</div>
										</button>	
										<button class="p-2 rounded-md bg-red-500 text-white hover:bg-red-600 mx-2" @click="deleteInvoice(item)"
											v-show="route().current('eta.invoices.sent.index') && item.status!='Valid' && item.status!='processing' && item.status!='approved'" 
										>
											<div>{{__('Delete')}}</div>
										</button>	
										<button class="p-2 rounded-md bg-red-500 text-white hover:bg-red-600 mx-2" @click="editInvoice(item)"
											v-show="route().current('eta.invoices.sent.index') && item.status!='Valid'"
										>
											<div>{{__('Edit')}}</div>
										</button>	
										<button class="p-2 rounded-md bg-red-500 text-white hover:bg-red-600 mx-2" @click="viewInvoice(item)">
											<div>{{__('View')}}</div>
										</button>	
										<button class="p-2 rounded-md bg-red-500 text-white hover:bg-red-600 mx-2" @click="downloadPDF(item)"
										>
											<div>PDF</div>
										</button>	
										<button class="p-2 rounded-md bg-red-500 text-white hover:bg-red-600 mx-2" @click="openExternal(item)"
											v-show="item.status=='Valid'"
										>
											<div>ETA</div>
										</button>	
<!--											<jet-button @click.prevent="editItem(item)">
											</jet-button> -->
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
    import AppLayout from '@/Layouts/AppLayout'
	import { InteractsWithQueryBuilder, Tailwind2 } from '@protonemedia/inertiajs-tables-laravel-query-builder';
    import JetButton from '@/Jetstream/Button';
	import AddEditItem from '@/Pages/Items/AddEdit';
	import Confirm from '@/UI/Confirm'
    import JetLabel from '@/Jetstream/Label'
	import PreviewInvoice from '@/Pages/Invoices/Preview';

    export default {
		mixins: [InteractsWithQueryBuilder],
        components: {
            AppLayout,
			Confirm,
			PreviewInvoice,
			JetLabel,
			Table: Tailwind2.Table,
			JetButton,
			AddEditItem,
        },
		props: {
			items: Object
  		},
        data() {
            return {
				invItem: {quantity: 1009 },
				cancelReason: ''
            }
        },
		methods: {
			openExternal(item) {
				window.open("https://invoicing.eta.gov.eg/print/documents/"+item.uuid+"/share/"+item.longId);
			},
			downloadPDF(item) {
				this.invItem = item;
				window.open(route('pdf.invoice.preview', [item.Id]));
			},
			editInvoice(item) {
				this.invItem = item;
				window.location.href = route('invoices.edit', [item.Id]);
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
                axios.post(route('eta.invoices.delete'), {Id: this.invItem.Id, 
				})
				.then(response => {
					location.reload();
                }).catch(error => {
					alert(error.response.data);
                });
				
			},
			cancelInvoice(item) {
				this.invItem = item;
				this.$refs.dlg2.ShowModal();
			},
			cancelInv2() {
                axios.post(route('eta.invoices.cancel'), {uuid: this.invItem.uuid, 
					status: 'cancelled',
					reason: this.cancelReason
				})
				.then(response => {
					location.reload();
					//alert(response.data);
                }).catch(error => {
					alert(error.response.data);
                    //this.$refs.password.focus()
                });
				
			},
			rejectInvoice(item) {
				this.invItem = item;
				this.$refs.dlg1.ShowModal();
			},
			rejectInv2() {
                axios.post(route('eta.invoices.cancel'), {uuid: this.invItem.uuid, 
					status: 'rejected',
					reason: this.cancelReason
				})
				.then(response => {
					alert(response.data);
                }).catch(error => {
					alert(error.response.data);
                    //this.$refs.password.focus()
                });
				
			},
			nestedIndex: function(item, key) {
				try {
					var keys = key.split(".");
					if (keys.length == 1)
						return item[key].toString();;
					if (keys.length == 2)
						return item[keys[0]][keys[1]].toString();
					if (keys.length == 3)
						return item[keys[0]][keys[1]][keys[2]].toString();
					return "Unsupported Nested Index";
				}
				catch(err) {
				}
				return "N/A";
			},
			editItem: function(item_id) {
				//alert(JSON.stringify(item_id));
			}
		}
    }
</script>
<style scoped>
:deep(table th) {
  text-align: start;
}
</style>
