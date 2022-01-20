<template>
    <app-layout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="card-title flex flex-col lg:flex-row justify-between p-3">
                    <h4 class="capitalize">
                        {{ __('Summary Report') }}
                    </h4>
                </div>
                <div class="bg-white shadow-xl sm:rounded-lg px-4 pb-4 pt-4">
					<div class="grid lg:grid-cols-4 gap-4 sm:grid-cols-1 h-1/2 overflow">
						<div>
							<jet-label :value="__('Branch')" />
							<multiselect v-model="form.issuer"   label="name" :options="branches" placeholder="Select branch" />
						</div>
						<div>
							<jet-label :value="__('Customer')" />
							<multiselect v-model="form.receiver" label="name" :options="customers" placeholder="Select customer" />
						</div>
						<TextField v-model="form.startDate" itemType="date" :itemLabel="__('Start Date')" />
						<TextField v-model="form.endDate"   itemType="date" :itemLabel="__('End Date')" />
					</div>
					<div class="flex items-center justify-end mt-4">
			    		<jet-secondary-button @click="onDownload()">
   							{{__('Download')}}
        				</jet-secondary-button>
	
		        		<jet-button class="ms-2" @click="onShow()" >
    		    			{{__('Show')}}
		        		</jet-button>
					</div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="result my-5 overflow-x-auto w-full" v-if="data.length > 0">
                    <table class="w-11/12 mx-auto max-w-4xl lg:max-w-full">
                        <thead class="text-center bg-gray-300">
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('Invoice Number') }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('Month') }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('Date') }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('Tax Total') }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('Client Name') }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('Total Amount') }}
                            </th>
                        </thead>
                        <tbody class="text-center border border-[#eceeef]">
                            <tr
                                class="border border-[#eceeef]"
                                v-for="row in data"
                                :key="row"
                            >
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.Id }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.Month }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.Date }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.TaxTotal }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.Client }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.Total }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <p class="text-center text-red-600 my-5">
                        <i class="fa fa-exclamation-circle mr-1"></i>
                        {{ __('No Records Were Found') }}
                    </p>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>

<script>
	import { computed, ref } from "vue";
    import AppLayout from '@/Layouts/AppLayout'
    import JetLabel from '@/Jetstream/Label'
    import JetButton from '@/Jetstream/Button'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetDangerButton from '@/Jetstream/DangerButton'
	import TextField from '@/UI/TextField'
	import Multiselect from '@suadelabs/vue3-multiselect'
	import DialogInvoiceLine from '@/Pages/Invoices/EditLine'

    export default {
        components: {
            AppLayout, JetLabel, JetButton, JetSecondaryButton, JetDangerButton, 
			DialogInvoiceLine,
			TextField, Multiselect 
        },
		props: {
			invoice:{
				Type: Object,
				default: null
			},
			items: {
				Type: Object,
				default: null
			}
		},
		data () {
            return {
				branches: [],
				customers: [],
				data: [],
				errors: [],
                form: this.$inertia.form({
					issuer: '',
					receiver: '',
					startDate: new Date().toISOString().slice(0, 10),
					endDate: new Date().toISOString().slice(0, 10),
				})
			}
		},
		methods: {
			onShow: function() {
				axios.post(route('reports.summary.details.data'), this.form)
                .then(response => {
					this.data = response.data;
                }).catch(error => {
                });

			},
			onDownload: function() {
				axios.post(route('reports.summary.details.download'), this.form)
                .then(response => {
                }).catch(error => {
                });

			},
		},
		created: function created() {
			axios.get(route('json.branches'))
			.then(response => {
				var temp = [{Id: -1, name: 'All'}];
				this.branches = temp.concat(response.data);
            }).catch(error => {
				console.log(error);
            });
			axios.get(route('json.customers'))
			.then(response => {
				var temp = [{Id: -1, name: 'All'}];
				this.customers = temp.concat(response.data);
            }).catch(error => {
				console.log(error);
            });
		}
    }
</script>

