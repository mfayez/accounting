<template>
    <jet-dialog-modal :show="showDialog" max-width="lg" @close="showDialog = false">
		<template #title>
        	{{__('Application Settings')}}
        </template>

        <template #content>
			<jet-validation-errors class="mb-4" />

			<form @submit.prevent="submit">
				<div class="grid grid-cols-2 gap-4">
					<div class="col-span-2">
						<jet-label for="serial_number" :value="__('Serial Number')" class="ms-2"/>
						<jet-input id="serial_number" type="text" 
							class="mt-1 block w-full" v-model="form.serial_number" 
							required autofocus
						/>
					</div>
					<div class="col-span-1">
                        <jet-checkbox name="e_invoice" id="e_invoice" v-model:checked="form.e_invoice" />
						<jet-label for="e_invoice" :value="__('E-Invoice')" class="ms-2"/>
					</div>
					<div class="col-span-1">
                        <jet-checkbox name="e_receipt" id="e_receipt" v-model:checked="form.e_receipt" />
						<jet-label for="e_receipt" :value="__('E-Receipt')" class="ms-2"/>
					</div>
					<div class="col-span-1">
                        <jet-checkbox name="inventory" id="inventory" v-model:checked="form.inventory" />
						<jet-label for="inventory" :value="__('Activate Inventories')" class="ms-2"/>
					</div>
					<div class="col-span-1">
                        <jet-checkbox name="accounting" id="accounting" v-model:checked="form.accounting" />
						<jet-label for="accounting" :value="__('Activate Accounting')" class="ms-2"/>
					</div>
					<div class="col-span-1">
                        <jet-checkbox name="sales_buzz" id="sales_buzz" v-model:checked="form.sales_buzz" />
						<jet-label for="sales_buzz" :value="__('Sales Buz Integration')" class="ms-2"/>
					</div>
					<div class="col-span-1">
                        <jet-checkbox name="mobis_integration" id="mobis_integration" v-model:checked="form.mobis_integration" />
						<jet-label for="mobis_integration" :value="__('Mobis Integration')" class="ms-2"/>
					</div>
					<div class="col-span-1">
                        <jet-checkbox name="custom_desc" id="custom_desc" v-model:checked="form.custom_desc" />
						<jet-label for="custom_desc" :value="__('Custom Items Description')" class="ms-2"/>
					</div>
					
					<div class="col-span-1">
						<jet-label :value="__('Currencies')" />
						<multiselect
							v-model="form.currencies"
							:options="currencies"
							:placeholder="__('Select Company Currencies')"
							:multiple="true"
						/>
					</div>

					<div></div>
					
					<div class="col-span-2">
                        <jet-checkbox name="automatic" id="automatic" v-model:checked="form.automatic" />
						<jet-label for="automatic" :value="__('Automatic Invoice Number')" class="ms-2"/>
					</div>

					<div class="col-span-1">
						<jet-label for="invoiceVersion" :value="__('Invoice Version')" />
						<jet-input id="invoiceVersion" type="text" 
							class="mt-1 block w-full" v-model="form.invoiceVersion" 
							v-model:active="form.automatic" v-model:required="form.automatic" autofocus />
					</div>
					
					<div class="col-span-1">
						<jet-label for="invoice_template" :value="__('Invoice Template')" />
						<jet-input id="invoice_template" type="text" 
							class="mt-1 block w-full" v-model="form.invoiceTemplate" 
							v-model:active="form.automatic" v-model:required="form.automatic" autofocus />
					</div>
				</div>
			</form>
		</template>
		<template #footer>
			<div class="flex items-center justify-end mt-4">
	    		<jet-secondary-button @click="CancelDlg()">
   					{{__('Cancel')}}
        		</jet-secondary-button>

	        	<jet-button class="ms-2" @click="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
    	    		{{__('Save')}}
	        	</jet-button>
			</div>
	   </template>
	</jet-dialog-modal>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>

<script>
	import JetActionMessage from "@/Jetstream/ActionMessage.vue";
	import JetActionSection from "@/Jetstream/ActionSection.vue";
	import JetButton from "@/Jetstream/Button.vue";
	import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
	import JetDangerButton from "@/Jetstream/DangerButton.vue";
	import JetDialogModal from "@/Jetstream/DialogModal.vue";
	import JetFormSection from "@/Jetstream/FormSection.vue";
	import JetInput from "@/Jetstream/Input.vue";
	import JetCheckbox from "@/Jetstream/Checkbox.vue";
	import JetInputError from "@/Jetstream/InputError.vue";
	import JetLabel from "@/Jetstream/Label.vue";
	import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
	import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
	import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
	import Multiselect from "@suadelabs/vue3-multiselect";

    export default {
        components: {
            JetActionMessage,JetActionSection,JetButton,JetConfirmationModal,JetDangerButton,
			JetDialogModal,JetFormSection,JetInput,JetCheckbox,JetInputError,JetLabel,JetSecondaryButton,
			JetSectionBorder,JetValidationErrors,Multiselect,
        },

        props: {
//            pUser: {
//				Type: Object,
//				default:null
//			},
        },

        data() {
            return {
				currencies: ["EGP", "USD", "EUR", "GBP"],
				settings: [],
				errors: [],
                form: this.$inertia.form({
					type: 'application settings',
					custom_desc: false,
                    automatic: false,
					e_invoice: true,
					e_receipt: false,
					sales_buzz: false,
					mobis_integration: false,
					accounting: false,
					inventory: false,
					serial_number: "00000000-00000000-00000000",
					invoiceTemplate: '',
					invoiceVersion: '1.0',
					currencies: ['EGP'],
                }),
				showDialog: false,
            }
        },

        methods: {
			ShowDialog() {
				this.showDialog = true;
				axios
					.get(route('settings.json'), { params: { type: this.form.type} })
					.then(response => {
						this.settings = response.data;
						this.form.invoiceTemplate = this.settings.invoiceTemplate;
						this.form.invoiceVersion = this.settings.invoiceVersion;
						this.form.currencies = JSON.parse(this.settings.currencies);
						this.form.automatic = this.settings.automatic == '1' ? true : false;
						this.form.custom_desc = this.settings.custom_desc == '1' ? true : false;
						this.form.e_invoice = this.settings.e_invoice == '1' ? true : false;
						this.form.e_receipt = this.settings.e_receipt == '1' ? true : false;
						this.form.sales_buzz = this.settings.sales_buzz == '1' ? true : false;
						this.form.mobis_integration = this.settings.mobis_integration == '1' ? true : false;
						this.form.accounting = this.settings.accounting == '1' ? true : false;
						this.form.inventory = this.settings.inventory == '1' ? true : false;
						this.form.serial_number = this.settings.serial_number;
        		    }).catch(error => {
            		});
			},
			CancelDlg() {
				this.showDialog = false;
			},
			SaveSettings() {
                axios.post(route('settings.store'), this.form)
				.then(response => {
                    this.$nextTick(() => this.$emit('dataUpdated'));
					this.form.reset();
					this.form.processing = false;
					this.showDialog = false;
                }).catch(error => {
                    this.form.processing = false;
					this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;
                    //this.$refs.password.focus()
                });

			},
            submit() {
				this.SaveSettings();
            }
        },
		created: function created() {
		},
    }
</script>

