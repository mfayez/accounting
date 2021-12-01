<template>
    <jet-dialog-modal :show="showDlg" max-width="3xl" @close="showDlg = false">
		<template #title>
        	Add item to the invoice
        </template>

        <template #content>
			<div class="grid grid-cols-4 gap-4 border-b border-gray-20 pb-4">
				<div class="col-span-2">
					<jet-label value="Item" />
					<multiselect v-model="item.item" :options="items" label="codeNamePrimaryLang" />
				</div>
				<div class="col-span-2">
					<jet-label value="Units" />
					<multiselect v-model="item.unit" :options="units" label="desc_en" />
				</div>
				<TextField v-model="item.quantity" itemType="number" itemLabel="Quantity" @update:model-value="updateValues()" />
				<TextField v-model="item.unitValue.amountEGP" itemType="number" itemLabel="Unit Price" @update:model-value="updateValues()" />
				<TextField v-model="item.salesTotal" itemType="number" itemLabel="Sales Total" :active="false"/>
				<TextField v-model="item.total" itemType="number" itemLabel="Total" :active="false"/>
				<TextField v-model="item.valueDifference" itemType="number" itemLabel="Value Difference" />
				<TextField v-model="item.totalTaxableFees" itemType="number" itemLabel="Total Taxable Fees" />
				<TextField v-model="item.netTotal" itemType="number" itemLabel="Net Total" :active="false"/>
				<TextField v-model="item.itemsDiscount" itemType="number" itemLabel="Items Discount" @update:model-value="updateValues()"/>
			</div>
			<div class="grid grid-cols-7 gap-2 mt-2 border-t border-b border-gray-20">
				<multiselect class="col-span-3"
					v-model="taxType" 
					label="label" 
					placeholder="Select Tax Type"
					:options="taxTypes" 
					@update:model-value="updateTaxSubtypes"
				/>
				<multiselect class="col-span-3"
					v-model="taxSubtype" 
					placeholder="Select Tax Subtype"
					:options="taxSubtypes1" 
					label="label" 
				/>
				<!--<pre> {{item}} </pre>-->
				<div class="flex items-center justify-center">
			    	<jet-secondary-button @click="AddTaxItem()">
   						Add Tax
        			</jet-secondary-button>
				</div>
			</div>
			<div class="grid grid-cols-7 gap-0 mt-2">
				<div class="bg-gray-200 col-span-2">Tax Code</div>
				<div class="bg-gray-200 col-span-2">Tax Subcode</div>
				<div class="bg-gray-200 col-span-1">Tax Amount</div>
				<div class="bg-gray-200 col-span-1">Tax Percentage</div>
				<div class="bg-gray-200 col-span-1"></div>
				<template v-for="(taxitem, idx1) in item.taxItems" :key="taxitem.key">
					<jet-label class="mt-2 col-span-2">{{taxitem.taxType.label}}</jet-label>
					<jet-label class="mt-2 col-span-2">{{taxitem.taxSubtype.label}}</jet-label>
					<jet-input :id="taxitem.key" type="number" class="mt-1 block w-full mt-2 col-span-1" 
						:isRounded="false" @update:model-value="updatePercentage(taxitem, $event)"
						v-model="taxitem.value" required autofocus />
					<jet-input :id="taxitem.key" type="number" class="mt-1 block w-full mt-2 col-span-1"
						:isRounded="false" @update:model-value="updateValue(taxitem, $event)"
						v-model="taxitem.percentage" required autofocus />
		    		<jet-danger-button @click="item.taxItems.splice(idx1, 1)" class="mt-2 ml-2">
   						Delete
	        		</jet-danger-button>				
				</template>
				<jet-label class="col-span-7" v-if="!item.taxItems || item.taxItems.length == 0">
					Please Add tax items if applicable
				</jet-label>
			</div>
		</template>
		<template #footer>
			<div class="flex items-center justify-end mt-4">
	    		<jet-secondary-button @click="CancelDlg()">
   					Cancel
        		</jet-secondary-button>

	        	<jet-button class="ml-2" @click="SaveItem()" >
    	    		Save
	        	</jet-button>
			</div>
	   </template>
	</jet-dialog-modal>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetButton from '@/Jetstream/Button'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetCheckbox from '@/Jetstream/Checkbox'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import JetValidationErrors from '@/Jetstream/ValidationErrors'
	import TextField from '@/UI/TextField'
	import Multiselect from '@suadelabs/vue3-multiselect'

    export default {
        components: {
			TextField, Multiselect, 
            JetActionMessage,
            JetActionSection,
            JetButton,
            JetConfirmationModal,
            JetDangerButton,
            JetDialogModal,
            JetFormSection,
            JetInput,
            JetCheckbox,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            JetSectionBorder,
			JetValidationErrors,
        },

        props: ['modelValue',
		],
        emits: ['update:modelValue'],

        data() {
            return {
				item: this.modelValue,
				count: 1,
				units:[],
				items: [],
				taxTypes: [], taxSubtypes: [], taxSubtypes1: [],
				taxType: null, taxSubtype: null,
				showDlg: false,
            }
        },

        methods: {
			ShowDialog() {
				this.item = JSON.parse(JSON.stringify(this.modelValue));
				this.showDlg = true;
			},
			CancelDlg() {
				this.showDlg = false;
			},
			AddTaxItem() {
				if (!this.item.taxItems || this.item.taxItems.length == 0)
					this.item.taxItems = [];
				this.item.taxItems.push({ taxType: this.taxType, taxSubtype: this.taxSubtype, 
											   value: 0, percentage: 0,  key: this.count});
				this.taxType = null;
				this.taxSubtype = null;
				this.count = this.count+1;
			},
			updateTaxSubtypes() {
				this.taxSubtypes1 = this.taxSubtypes.filter((obj) => {
					if (obj.TaxtypeReference == this.taxType.Code)
						return obj;
				});
				this.subType = {};
			},
			calculateTax(){
				this.item.total = this.item.netTotal + 0;
				if (this.item.taxItems) {
					for (var j = 0; j< this.item.taxItems.length; j++)
					{
						var taxitem = this.item.taxItems[j];
						taxitem.value = taxitem.percentage * this.item.total / 100.0
						this.item.total += taxitem.value;
					}
				}
			},
			parse(val){
				var temp = parseFloat(val);
				if (isNaN(temp))
					temp = 0;
				return temp;
			},
			updateValues(){
				this.item.salesTotal = this.parse(this.item.quantity) * this.parse(this.item.unitValue.amountEGP);
				this.item.netTotal = this.item.salesTotal - this.parse(this.item.itemsDiscount);
				this.calculateTax();
			},
			updateValue(item, val) {
				item.value = this.item.netTotal * val / 100.0;
				this.calculateTax();
			},
			updatePercentage(item, val) {
				item.percentage = val * 100.0 / this.item.netTotal;
				this.calculateTax();
			},
			SaveItem() {
				this.item.isDirty = true;
				this.showDlg = false;
				this.$emit('update:modelValue',this.item);
			},
        },
		created: function created() {
			axios.get(route('json.eta.items'))
			.then(response => {
				this.items = response.data;
            }).catch(error => {});
			axios.get('/json/UnitTypes.json')
			.then(response => {
				this.units = response.data;
            }).catch(error => {});
			axios.get('/json/TaxSubtypes.json')
			.then(response => {
				this.taxSubtypes = response.data;
				this.taxSubtypes = this.taxSubtypes.map((obj) => {
					obj.label = obj.Code.concat('(' ,  obj.Desc_ar , ')');
					return obj;
				});
            }).catch(error => {});
			axios.get('/json/TaxTypes.json')
			.then(response => {
				this.taxTypes = response.data;
				this.taxTypes = this.taxTypes.map((obj) => {
					obj.label = obj.Code.concat('(' ,  obj.Desc_ar , ')');
					return obj;
				});
            }).catch(error => {});
		}
    }
</script>

