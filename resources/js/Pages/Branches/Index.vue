<template>
    <app-layout>
		<edit-branch ref="dlg2" :branch="branch"/>
		<confirm ref="dlg1" @confirmed="remove()">
			{{__('Are you sure you want to delete this branch?')}}
		</confirm>
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
								<th v-show="showColumn('Id')"  @click.prevent="sortBy('Id')">{{__('ID')}}</th>
								<th v-show="showColumn('name')"  @click.prevent="sortBy('name')">{{__('Name')}}</th>
								<th v-show="showColumn('receiver_id')" @click.prevent="sortBy('receiver_id')">{{__('Registration Number')}}</th>

								<th v-show="showColumn('type')" @click.prevent="sortBy('type')">{{__('Type(B|P)')}}</th>
								<th @click.prevent="">{{__('Actions')}}</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="branch in branches.data" :key="branch.id">
									<td v-show="showColumn('Id')">{{ branch.Id }}</td>
									<td v-show="showColumn('name')">{{ branch.name }}</td>
									<td v-show="showColumn('receiver_id')">{{ branch.receiver_id }}</td>
									<td v-show="showColumn('type')">{{ branch.type == 'B' ? __('Business') : __('Individual')  }}</td>
									<td>
										<button class="p-1 rounded-md bg-green-500 text-white hover:bg-green-600 mx-2" @click="editBranch(branch)">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
										</button>
										<button class="p-1 rounded-md bg-red-500 text-white hover:bg-red-600 mx-2" i @click="removeBranch(branch)">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
										</button>	
										
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
	import Confirm from '@/UI/Confirm'
	import EditBranch from '@/Pages/Branches/Edit'
	import { InteractsWithQueryBuilder, Tailwind2 } from '@protonemedia/inertiajs-tables-laravel-query-builder';


    export default {
		mixins: [InteractsWithQueryBuilder],
        components: {
            AppLayout, Confirm, EditBranch,
			Table: Tailwind2.Table,
        },
		props: {
			branches: Object
  		},
        data() {
            return {
				branch: Object
            }
        },
		methods: {
			editBranch(cust) {
				this.branch = cust;
				this.$nextTick(() => this.$refs.dlg2.ShowDialog());
				//this.$refs.dlg2.ShowDialog();
			},
			removeBranch(cust) {
				this.branch = cust;
				this.$refs.dlg1.ShowModal();
			},
			remove() {
                axios.delete(route('branches.destroy', {'branch': this.branch.Id}))
				.then(response => {
					location.reload();
                }).catch(error => {
                    //this.$refs.password.focus()
                });
				
			},

		}
    }
</script>
<style scoped>
:deep(table th) {
  text-align: start;
}
</style>
