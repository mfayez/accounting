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
								<th v-show="showColumn('registration_number')" @click.prevent="sortBy('registration_number')">Registration Number</th>
								<th v-show="showColumn('address_country')" @click.prevent="">Country</th>
								<th v-show="showColumn('address_governate')" @click.prevent="">Governate</th>
								<th v-show="showColumn('address_regionCity')" @click.prevent="">City</th>
								<th v-show="showColumn('address_street')" @click.prevent="">Street</th>
								<th v-show="showColumn('address_building_number')" @click.prevent="">Building Number</th>
								<th @click.prevent="">Actions</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="customer in customers.data" :key="customer.id">
									<td v-show="showColumn('name')">{{ customer.name }}</td>
									<td v-show="showColumn('registration_number')">{{ customer.registration_number }}</td>
									<td v-show="showColumn('address_country')">{{ customer.address_country }}</td>
									<td v-show="showColumn('address_governate')">{{ customer.address_governate }}</td>
									<td v-show="showColumn('address_regionCity')">{{ customer.address_regionCity }}</td>
									<td v-show="showColumn('address_street')">{{ customer.address_street }}</td>
									<td v-show="showColumn('address_building_number')">{{ customer.address_building_number }}</td>
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
