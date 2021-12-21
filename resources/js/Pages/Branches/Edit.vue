<template>
    <jet-dialog-modal :show="showDialog" @close="showDialog = false">
		<template #title>
        	{{__('Branch Information')}}
        </template>

        <template #content>
			<jet-validation-errors class="mb-4" />

			<form @submit.prevent="submit">
				<div class="grid grid-cols-2 gap-4">
					<div>
						<div>
							<jet-label for="branchNo" :value="__('Branch Number')" />
							<jet-input id="branchNo" type="number" class="mt-1 block w-full" v-model="form.address.branchID" required autofocus />
						</div>
						<div class="mt-4">
							<jet-label for="id" :value="__('Tax Registration Number')" />
							<jet-input id="id" type="number" class="mt-1 block w-full" v-model="form.issuer_id" required />
						</div>
							<div class="mt-4">
							<jet-label for="name" :value="__('Branch Name')" />
							<jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
						</div>
						<div class="mt-4">
							<jet-label for="additionalInformation" :value="__('Additional Information (Location)')" />
							<jet-input id="additionalInformation" type="text" class="mt-1 block w-full" v-model="form.address.additionalInformation" />
						</div>
						<div class="mt-4">
							<jet-label for="postalCode" :value="__('Postal Code')" />
							<jet-input id="postalCode" type="number" class="mt-1 block w-full" v-model="form.address.postalCode" />
						</div>
					</div>
					<div>
						<div>
							<jet-label for="country" :value="__('Country')" />
							<jet-input id="country" type="text" class="mt-1 block w-full" v-model="form.address.country" required />
						</div>
						<div class="mt-4">
							<jet-label for="governate" :value="__('Governate/State')" />
							<jet-input id="governate" type="text" class="mt-1 block w-full" v-model="form.address.governate" required />
						</div>
						<div class="mt-4">
							<jet-label for="regionCity" :value="__('Region/City')" />
							<jet-input id="regionCity" type="text" class="mt-1 block w-full" v-model="form.address.regionCity" required />
						</div>
						<div class="mt-4">
							<jet-label for="street" :value="__('Street')" />
							<jet-input id="street" type="text" class="mt-1 block w-full" v-model="form.address.street" required />
						</div>
						<div class="mt-4">
							<jet-label for="buildingNumber" :value="__('Building Number')" />
							<jet-input id="buildingNumber" type="number" class="mt-1 block w-full" v-model="form.address.buildingNumber" required />
						</div>
					</div>
				</div>
			</form>
		</template>
		<template #footer>
			<div class="flex items-center justify-end mt-4">
	    		<jet-secondary-button @click="CancelAddBranch()">
   					{{__('Cancel')}}
        		</jet-secondary-button>

	        	<jet-button class="ms-2" @click="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
    	    		{{__('Save')}}
	        	</jet-button>
			</div>
	   </template>
	</jet-dialog-modal>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetButton from '@/Jetstream/Button'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetCheckbox from '@/Jetstream/Checkbox'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetSectionBorder from '@/Jetstream/SectionBorder'
    import JetValidationErrors from '@/Jetstream/ValidationErrors'

    export default {
        components: {
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

        props: {
            branch: {
				Type: Object,
				default:null
			},
        },

        data() {
            return {
				errors: [],
                form: this.$inertia.form({
                    name: '',
                    issuer_id: '',
                    type: 'B',
					address: {
						branchID: '',
						country: '',
						governate: '',
						regionCity: '',
						street: '',
						buildingNumber: '',
						postalCode : '',
						additionalInformation: '',

					}
                }),
				showDialog: false,
            }
        },

        methods: {
			ShowDialog() {
				if (this.branch !== null){
					this.form.name = this.branch.name;
					this.form.issuer_id = this.branch.issuer_id;
					this.form.type = this.branch.type;
					this.form.address.branchID = this.branch.address.branchID;
					this.form.address.country = this.branch.address.country;
					this.form.address.governate = this.branch.address.governate;
					this.form.address.regionCity = this.branch.address.regionCity;
					this.form.address.street = this.branch.address.street;
					this.form.address.buildingNumber = this.branch.address.buildingNumber;
					this.form.address.postalCode = this.branch.address.postalCode;
					this.form.address.additionalInformation = this.branch.address.additionalInformation;
				}
				this.showDialog = true;
			},
			CancelAddBranch() {
				this.showDialog = false;
			},
			SaveBranch() {
                axios.put(route('branches.update', {'branch': this.branch.Id}), this.form)
				.then(response => {
					location.reload();
                }).catch(error => {
                    this.form.processing = false;
					this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;//.password[0];
                    //this.$refs.password.focus()
                });
			},
			SaveNewBranch() {
                axios.post(route('branches.store'), this.form)
				.then(response => {
                    this.processing = false;
                    this.$nextTick(() => this.$emit('dataUpdated'));
					this.form.reset();
					this.form.processing = false;
					this.showDialog = false;
                }).catch(error => {
                    this.form.processing = false;
					this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;//.password[0];
                    //this.$refs.password.focus()
                });
			},
            submit() {
				if (this.branch == null)
					this.SaveNewBranch();
				else
					this.SaveBranch();
            }
        },
    }
</script>

