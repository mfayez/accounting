<template>
    <app-layout>
        <div class="py-4">
			<dialog-invoice-line v-model="currentItem" ref="dlg1" @update:model-value="onClose"/>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 pb-4 pt-0">
					<div class="flex items-center ml-0 mb-4 border-b border-gray-200">
						<jet-button @click="tab_idx=1" :disabled="tab_idx==1" :isRounded="false">
							Invoice Summary
						</jet-button>
						<jet-button @click="tab_idx=2" :disabled="tab_idx==2" :isRounded="false">
							Optional Data (PO, SO)
						</jet-button>
						<jet-button @click="tab_idx=3" :disabled="tab_idx==3" :isRounded="false">
							Invoice Items
						</jet-button>
					</div>
					<!--First Tab-->
					<div v-show="tab_idx==1" class="grid lg:grid-cols-4 gap-4 sm:grid-cols-1 h-1/2 overflow">
						<div>
							<jet-label value="Branch" />
							<multiselect v-model="form.issuer"   label="name" :options="branches" placeholder="Select branch" />
						</div>
						<div>
							<jet-label value="Customer" />
							<multiselect v-model="form.receiver" label="name" :options="customers" placeholder="Select customer" />
						</div>
						<div class="lg:col-span-2">
							<jet-label value="Branch Activity" />
							<multiselect v-model="form.taxpayerActivityCode" label="Desc_ar" :options="activities" placeholder="Select activity" />
						</div>
						<TextField v-model="form.dateTimeIssued" itemType="datetime-local" itemLabel="Invoice Date" />
						<TextField v-model="form.internalID" itemType="text" itemLabel="Internal Invoice ID" />
						<TextField v-model="form.totalSalesAmount" itemType="number" itemLabel="Total Sales Amount" />
						<TextField v-model="form.totalDiscountAmount" itemType="number" itemLabel="Total Discount Amount" />
						<TextField v-model="form.netAmount" itemType="number" itemLabel="Net Amount" />
						<TextField v-model="form.totalAmount" itemType="number" itemLabel="Total Amount" />
						<TextField v-model="form.extraDiscountAmount" itemType="number" itemLabel="Extra Discount Amount" />
						<TextField v-model="form.totalItemsDiscountAmount" itemType="number" itemLabel="Total Items Discount Amount" />
					</div>
					<!--second tab-->
					<div v-show="tab_idx==2" class="grid lg:grid-cols-4 gap-4 sm:grid-cols-1 h-1/2 overflow">
						<TextField v-model="form.purchaseOrderReference" itemType="text" itemLabel="Purchase Order" />
						<TextField v-model="form.purchaseOrderDescription" itemType="text" itemLabel="Purchase Order Description" />
						<TextField v-model="form.salesOrderReference" itemType="text" itemLabel="Sales Order" />
						<TextField v-model="form.salesOrderDescription" itemType="text" itemLabel="Sales Order Description" />
						<TextField v-model="form.purchaseOrderReference" itemType="text" itemLabel="Purchase Order Reference" />
						<TextField v-model="form.proformaInvoiceNumber" itemType="text" itemLabel="Proforma Invoice Number" />
					</div>
					<!--third tab-->
					<div v-show="tab_idx==3">
						<div class="grid grid-cols-10 gap-0 mt-2">
							<div class="bg-gray-200 col-span-3">Item</div>
							<div class="bg-gray-200 col-span-1">Unit Price</div>
							<div class="bg-gray-200 col-span-1">Quantity</div>
							<div class="bg-gray-200 col-span-1">Sales Total</div>
							<div class="bg-gray-200 col-span-1">Net Total</div>
							<div class="bg-gray-200 col-span-1">Tax Items</div>
							<div class="bg-gray-200 col-span-1"></div>
							<div class="bg-gray-200 col-span-1"></div>
							<template v-for="(item, idx1) in form.invoiceLines">
								<jet-label class="mt-2 col-span-3">{{item.item.codeNamePrimaryLang}}</jet-label>
								<jet-label class="mt-2 col-span-1">{{item.unitValue}}</jet-label>
								<jet-label class="mt-2 col-span-1">{{item.quantity}}</jet-label>
								<jet-label class="mt-2 col-span-1">{{item.salesTotal}}</jet-label>
								<jet-label class="mt-2 col-span-1">{{item.netTotal}}</jet-label>
								<div class="grid grid-cols-2 gap-1">
									<template v-for="(taxitem, idx1) in item.taxItems" :key="taxitem.key">
										<jet-label class="mt-2 col-span-2">{{taxitem.taxType.Code}}({{taxitem.taxSubtype.Code}})</jet-label>
										<jet-label class="mt-2 col-span-2">{{taxitem.value}}({{taxitem.percentage}}%)</jet-label>
									</template>
								</div>
								<jet-secondary-button @click="EditItem(item, idx1)" class="mt-2 ml-2">
									Edit
								</jet-secondary-button>				
								<jet-danger-button @click="item.taxItems.splice(idx1, 1)" class="mt-2 ml-2">
									Delete
								</jet-danger-button>				
							</template>
							<jet-label class="col-span-8" v-if="!form.invoiceLines.length">
								Please Add tax items if applicable
							</jet-label>
						</div>
						<div class="flex items-center justify-end mt-4">
							<jet-button class="ml-2" @click="AddItem()">
								Add New Item
							</jet-button>
						</div>
						<div v-for="(item, idx1) in form.invoiceLines" class="border border-black">
							<!--<pre>{{item}}</pre>-->
						</div>
					</div>
					<div class="flex items-center justify-end mt-20">
			    		<jet-secondary-button @click="onCancel()">
   							Cancel
        				</jet-secondary-button>
	
		        		<jet-button class="ml-2" @click="onSave()" >
    		    			Save
		        		</jet-button>
					</div>
                </div>
				<!--<pre> {{form}} </pre>-->
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
		data () {
            return {
				addindNewLine: false,
				currentItemIdx: 0,
				tab_idx: 1,
				currentItem: {quantity: 1009 },
				branches: [],
				customers: [],
				activities: [],
				errors: [],
                form: this.$inertia.form({
					issuer: '',
					receiver: '',
					name: '',
					dateTimeIssued: new Date().toISOString().slice(0, 16),
					taxpayerActivityCode: '',
					internalID: '',
					purchaseOrderReference: '',
					purchaseOrderDescription: '',
					salesOrderReference: '',
					salesOrderDescription: '',
					purchaseOrderReference: '',
					proformaInvoiceNumber: '',
					totalSalesAmount: 0,
					totalDiscountAmount: 0,
					netAmount: 0,
					totalAmount: 0,
					extraDiscountAmount: 0,
					totalItemsDiscountAmount: 0,
					invoiceLines: [],
					taxTotals: []
				})
			}
		},
		props: {
			items: Object
  		},
		methods: {
			RecalculateTax: function() {
				var taxTotals = {};
				this.form.taxTotals = [];
				for (var i = 0; i < this.form.invoiceLines.length; i++)
				{
					var item = this.form.invoiceLines[i];
					for (var j = 0; j< item.taxItems.length; j++)
					{
						var taxitem = item.taxItems[j];
						if (taxitem.taxType.Code in taxTotals)
							taxTotals[taxitem.taxType.Code] = taxTotals[taxitem.taxType.Code] + parseFloat(taxitem.value);
						else
							taxTotals[taxitem.taxType.Code] = parseFloat(taxitem.value);
					}
				}
				for (let item of Object.keys(taxTotals))
					this.form.taxTotals.push({ taxType: item, amount: taxTotals[item] });
			},
			AddItem: function() {
				this.addingNewLine = true;
				this.currentItem = { quantity: 1, itemsDiscount: 0, valueDifference: 0};
				this.$nextTick(() => {
					this.$refs.dlg1.ShowDialog();
				});
				this.RecalculateTax();
			},
			EditItem: function(item, idx) {
				this.addingNewLine = false;
				this.currentItem = item;
				this.currentItemIdx = idx;
				this.$nextTick(() => {
					this.$refs.dlg1.ShowDialog();
				});
				this.RecalculateTax();
			},
			onClose: function() {
				this.currentItem.description = this.currentItem.item.descriptionPrimaryLang;
				this.currentItem.itemType= this.currentItem.item.codeTypeName;
				this.currentItem.itemCode= this.currentItem.item.itemCode;
				this.currentItem.unitType= this.currentItem.unit.code;
				this.currentItem.internalCode= this.currentItem.item.Id.toString();
				var temp = this.currentItem.unitValue;
				this.currentItem.unitValue = {};
				this.currentItem.unitValue.currencySold= 'EGP';
				this.currentItem.unitValue.amountEGP= temp;
				this.currentItem.taxableItems = this.currentItem.taxItems.map(function(taxitem) {
					var obj = {};
					obj.taxType = taxitem.taxType.Code;
					obj.amount  = taxitem.value;
					obj.subType = taxitem.taxSubtype.Code;
					obj.rate    = taxitem.percentage;
					return obj;
				});
				//delete this.currentItem.item;
				if (this.addingNewLine)
					this.form.invoiceLines.push(this.currentItem);
				else
					this.form.invoiceLines[this.currentItemIdx] = this.currentItem;
				this.addingNewLine = false;
			},
			onCancel: function() {
				window.history.back();
			},
			onSave: function() {
				axios.post(route('eta.invoices.store'), this.form)
                .then(response => {
                    this.processing = false;
                    this.$nextTick(() => this.$emit('dataUpdated'));
                    this.form.reset();
                    this.form.processing = false;
                    this.addingNew = false;
                }).catch(error => {
                    this.form.processing = false;
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;//.password[0];
                    //this.$refs.password.focus()
                });

			},
		},
		created: function created() {
			axios.get(route('json.branches'))
			.then(response => {
				this.branches = response.data;
            }).catch(error => {

            });
			axios.get(route('json.customers'))
			.then(response => {
				this.customers = response.data;
            }).catch(error => {

            });
			axios.get('/json/ActivityCodes.json')
			.then(response => {
				this.activities = response.data;
            }).catch(error => {

            });
		}
    }
</script>

