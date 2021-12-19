<template>
    <app-layout>
		<edit-user ref="dlg2" :branch="user"/>
		<confirm ref="dlg1" @confirmed="remove()">
			{{__('Are you sure you want to delete this user?')}}
		</confirm>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
					<Table
						:filters="queryBuilderProps.filters"
						:search="queryBuilderProps.search"
						:columns="queryBuilderProps.columns"
						:on-update="setQueryBuilder"
						:meta="users"
				  	>
						<template #head>
						  	<tr>
								<th v-show="showColumn('Id')"  @click.prevent="sortBy('Id')">{{__('ID')}}</th>
								<th v-show="showColumn('name')"  @click.prevent="sortBy('name')">{{__('Name')}}</th>
								<th v-show="showColumn('email')" @click.prevent="sortBy('email')">{{__('Email')}}</th>
								<th @click.prevent="">{{__('Actions')}}</th>
							</tr>
						</template>

						<template #body>
					  		<tr v-for="user in users.data" :key="user.id">
									<td v-show="showColumn('Id')">{{ user.id }}</td>
									<td v-show="showColumn('name')">{{ user.name }}</td>
									<td v-show="showColumn('email')">{{ user.email }}</td>
									<td>
										<button class="p-1 rounded-md bg-green-500 text-white hover:bg-green-600 mx-2" @click="edituser(user)">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
										</button>
										<button class="p-1 rounded-md bg-red-500 text-white hover:bg-red-600 mx-2" i @click="removeuser(user)">
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
	import EditUser from '@/Pages/Users/Edit'
	import { InteractsWithQueryBuilder, Tailwind2 } from '@protonemedia/inertiajs-tables-laravel-query-builder';


    export default {
		mixins: [InteractsWithQueryBuilder],
        components: {
            AppLayout, Confirm, EditUser,
			Table: Tailwind2.Table,
        },
		props: {
			users: Object
  		},
        data() {
            return {
				user: Object
            }
        },
		methods: {
			edituser(cust) {
				this.user = cust;
				this.$nextTick(() => this.$refs.dlg2.ShowDialog());
				//this.$refs.dlg2.ShowDialog();
			},
			removeuser(cust) {
				this.user = cust;
				this.$refs.dlg1.ShowModal();
			},
			remove() {
                axios.delete(route('users.destroy', {'user': this.user.id}))
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
