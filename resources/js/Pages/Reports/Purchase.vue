<template>
    <app-layout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="card-title flex flex-col lg:flex-row justify-between p-3">
                    <h4 class="capitalize">
                        {{ __('Purchase Report') }}
                    </h4>
                </div>
                <div class="bg-white shadow-xl sm:rounded-lg px-4 pb-4 pt-4">
					<div class="grid lg:grid-cols-3 gap-4 sm:grid-cols-1 h-1/2 overflow">
                        <div>
                            <jet-label :value="__('Vendor')" />
                            <multiselect
                                v-model="form.vendor"
                                label="name"
                                :options="vendors"
                                placeholder="Select vendor"
                            />
                        </div>
						<TextField v-model="form.startDate" itemType="date" :itemLabel="__('Start Date')" />
						<TextField v-model="form.endDate"   itemType="date" :itemLabel="__('End Date')" />
					</div>
					<div class="flex items-center justify-start mt-4">
			    		<jet-button @click="onShow()" >
    		    			{{__('Show')}}
		        		</jet-button>

                        <jet-secondary-button class="ms-2" @click="onDownload()">
   							{{__('Download')}}
        				</jet-secondary-button>
	
		        		<jet-secondary-button class="ms-2" @click="onDownloadSummary()">
   							{{__('Download Summary')}}
        				</jet-secondary-button>

                        <jet-secondary-button class="ms-2" @click="onArchive">
                            {{ __("Archive") }}
                        </jet-secondary-button>
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
                                <input type="checkbox" v-model="allChecked" v-on:change="checkAll()" />
                                {{ __("Archive") }}
                            </th>
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
                                    <input type="checkbox" v-model="row.archive" v-on:change="updateCheckBoxes()"/>
                                </td>
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
                                    {{ (Math.round(100*(row.Total-row.Net)) / 100).toFixed(2) }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.Seller }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ parseFloat(row.Total).toFixed(2) }}
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
    import swal from "sweetalert"

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
				data: [],
				errors: [],
                vendors: [],
                form: this.$inertia.form({
					startDate: new Date().toISOString().slice(0, 10),
					endDate: new Date().toISOString().slice(0, 10),
				})
			}
		},
		methods: {
			onShow: function() {
				axios.post(route('reports.summary.purchase.data'), this.form)
                .then(response => {
					this.data = response.data;
                    this.data.forEach((row) => {
                        row.archive = false;
                    });
                    this.allChecked = false;
                }).catch(error => {
                });

			},
			onDownload: function() {
				axios({
					url: route('reports.summary.purchase.download'), 
					method: 'POST',
					data: this.form,
					responseType: 'blob',
				}).then((response) => {
					const url = window.URL.createObjectURL(new Blob([response.data]));
					const link = document.createElement('a');
					link.href = url;
					link.setAttribute('download', 'report.xlsx');
					document.body.appendChild(link);
					link.click();
				});
			},
            onDownloadSummary: function() {
				axios({
					url: route('reports.summary.purchase.download2'), 
					method: 'POST',
					data: this.form,
					responseType: 'blob',
				}).then((response) => {
					const url = window.URL.createObjectURL(new Blob([response.data]));
					const link = document.createElement('a');
					link.href = url;
					link.setAttribute('download', 'report.xlsx');
					document.body.appendChild(link);
					link.click();
				});
			},
            checkAll() {
                this.$nextTick(() => {
                    this.data.forEach((row) => {
                        row.archive = this.allChecked;
                    });
                });
            },
            updateCheckBoxes() {
                //next tick
                this.$nextTick(() => {
                    this.allChecked = this.data.every((row) => row.archive);
                });
            },
            onArchive() {
                let selected = this.data.filter((row) => row.archive);
                let ids = selected.map((row) => row.LID);
                if (selected.length > 0) {
                    window.open(route("pdf.purchases", ids.join(',')));
                } else {
                    swal({
                        title:
                            document.body.lang == "en"
                                ? "Please Select At Least One Record"
                                : "برجاء اختيار فاتورة واحدة على الأقل",
                        icon: "error",
                    });
                }
            },
		},
		created: function created() {
            axios
            .get(route("json.eta.vendors"))
            .then((response) => {
                var temp = [{ id: -1, name: "All" }];
                this.vendors = temp.concat(response.data);
                this.form.vendor = this.vendors[0];
            })
            .catch((error) => {
                console.log(error);
            });
		}
    }
</script>

