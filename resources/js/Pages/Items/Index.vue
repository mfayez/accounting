<template>
    <app-layout>
        <div class="py-4">
            <div class="mx-auto sm:px-6 lg:px-8">
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
								<th v-show="showColumn('description')" @click.prevent="sortBy('description')">Description</th>
								<th v-show="showColumn('internal_code')" @click.prevent="sortBy('internal_code')">Internal Code</th>
								<th v-show="showColumn('gs1_code')" @click.prevent="sortBy('gs1_code')">Global Standard Code</th>
								<th v-show="showColumn('egs_code')" @click.prevent="sortyBy('egs_code')">Egyptian Standard Code</th>
								<th v-show="showColumn('unit_type')" @click.prevent="">Unit Type</th>
								<th v-show="showColumn('unit_value')" @click.prevent="">Unit Price</th>
								<th @click.prevent="">Actions</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="item in items.data" :key="item.id">
									<td v-show="showColumn('description')">{{ item.description }}</td>
									<td v-show="showColumn('internal_code')">{{ item.internal_code }}</td>
									<td v-show="showColumn('gs1_code')">{{ item.gs1_code }}</td>
									<td v-show="showColumn('egs_code')">{{ item.egs_code }}</td>
									<td v-show="showColumn('unit_type')">{{ item.unit_type }}</td>
									<td v-show="showColumn('unit_value')">{{ item.unit_value }}</td>
									<td>
										<div class="grid justify-items-center">
                    						<add-edit-item :item="item">
		                        				<jet-button type="button" >
												<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#FFFFFF"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14.06 9.02l.92.92L5.92 19H5v-.92l9.06-9.06M17.66 3c-.25 0-.51.1-.7.29l-1.83 1.83 3.75 3.75 1.83-1.83c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.2-.2-.45-.29-.71-.29zm-3.6 3.19L3 17.25V21h3.75L17.81 9.94l-3.75-3.75z"/></svg>
						                        </jet-button>
						                    </add-edit-item>
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

    export default {
		mixins: [InteractsWithQueryBuilder],
        components: {
            AppLayout,
			Table: Tailwind2.Table,
			JetButton,
			AddEditItem,
        },
		props: {
			items: Object
  		},
		methods: {
			editItem: function(item_id) {
				//alert(JSON.stringify(item_id));
			}
		}
    }
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
</style>
