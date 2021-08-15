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
						:meta="branches"
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
					  		<tr v-for="branch in branches.data" :key="branch.id">
									<td v-show="showColumn('name')">{{ branch.name }}</td>
									<td v-show="showColumn('receiver_id')">{{ branch.receiver_id }}</td>
									<td v-show="showColumn('type')">{{ branch.type == 'B' ? 'Business' : 'Individual'  }}</td>
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
			branches: Object
  		},
    }
</script>
