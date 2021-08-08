<template>
    <app-layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-12">
					<Table
						:filters="queryBuilderProps.filters"
						:search="queryBuilderProps.search"
						:columns="queryBuilderProps.columns"
						:on-update="setQueryBuilder"
						:meta="customers"
				  	>
						<template #head>
						  	<tr>
								<th @click.prevent="sortBy('name')">Name</th>
								<th v-show="showColumn('registration_code')" @click.prevent="sortBy('registration_code')">Registration Number</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="customer in customers.data" :key="customer.id">
									<td>{{ customer.name }}</td>
									<td v-show="showColumn('registration_code')">{{ customer.registration_code }}</td>
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
