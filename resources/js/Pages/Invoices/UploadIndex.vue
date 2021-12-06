<template>
    <app-layout>
		<confirm ref="dlg1" @confirmed="cancelUpload2">
			<jet-label for="type"  value="Are you sure you want to delete this file?" />
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
								<th @click.prevent="">Actions</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="item in items.data" :key="item.id">
									<td v-for="(col, key) in queryBuilderProps.columns" :key="key" v-show="showColumn(key)">
										<div v-for="rowVals in nestedIndex(item, key).split(',')">{{ rowVals }}</div>
									</td>
									<td>
										<div class="grid justify-items-center">
										<button class="p-1 rounded-md bg-red-500 text-white hover:bg-green-600 mx-2" @click="reviewUpload(item)"
											v-show="item.status == 'pending'"
										>
											{{__('Review')}}
										</button>	
										<button class="p-1 rounded-md bg-red-500 text-white hover:bg-red-600 mx-2" @click="cancelUpload(item)"
											v-show="item.status == 'pending' || item.status == 'uploading'"
										>
											{{__('Cancel')}}
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

    export default {
		mixins: [InteractsWithQueryBuilder],
        components: {
            AppLayout,
			Confirm,
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
				uploadItem: Object,
				cancelReason: ''
            }
        },
		methods: {
			cancelUpload(item) {
				this.uploadItem = item;
				this.$refs.dlg1.ShowModal();
			},
			cancelUpload2() {
                axios.post(route('eta.invoices.upload.cancel'), {id: this.uploadItem.Id, 
					status: 'cancelled'
				})
				.then(response => {
					alert(response.data);
                }).catch(error => {
					alert(error.response.data);
                    //this.$refs.password.focus()
                });
				
			},
			reviewUpload(item) {
				window.location.href = route('eta.invoices.sent.index',{upload_id: item.Id});
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
