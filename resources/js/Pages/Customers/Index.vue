<template>
    <app-layout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
					<Table
						:filters="queryBuilderProps.filters"
						:search="queryBuilderProps.search"
						:columns="queryBuilderProps.columns"
						:on-update="setQueryBuilder"
						:meta="customers"
				  	>
						<template #head>
						  	<tr>
								<th v-show="showColumn('name')"  @click.prevent="sortBy('name')">Name</th>
								<th v-show="showColumn('receiver_id')" @click.prevent="sortBy('receiver_id')">Registration Number</th>

								<th v-show="showColumn('type')" @click.prevent="sortBy('type')">Type(B|I)</th>
								<th @click.prevent="">Actions</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="customer in customers.data" :key="customer.id">
									<td v-show="showColumn('name')">{{ customer.name }}</td>
									<td v-show="showColumn('receiver_id')">{{ customer.receiver_id }}</td>
									<td v-show="showColumn('type')">{{ customer.address.country }}</td>
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
    import AppLayout from '@/Layouts/AppLayout'
	import { InteractsWithQueryBuilder, Tailwind2 } from '@protonemedia/inertiajs-tables-laravel-query-builder';


    export default {
		mixins: [InteractsWithQueryBuilder],
        components: {
            AppLayout,
			Table: Tailwind2.Table,
        },
		props: {
			customers: Object
  		},
    }
</script>
