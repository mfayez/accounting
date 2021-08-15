<template>
    <jet-dialog-modal :show="addingNew" @close="addingNew = false">
		<template #title>
        	Add new branch
        </template>

        <template #content>
			<jet-validation-errors class="mb-4" />

			<form @submit.prevent="submit">
				<div class="grid grid-cols-2 gap-4">
					<div>
						<div>
							<jet-label for="branchNo" value="Branch Number" />
							<jet-input id="branchNo" type="number" class="mt-1 block w-full" v-model="form.address.branchId" required autofocus />
						</div>
						<div class="mt-4">
							<jet-label for="id" value="Tax Registration Number" />
							<jet-input id="id" type="number" class="mt-1 block w-full" v-model="form.issuer_id" required />
						</div>
							<div class="mt-4">
							<jet-label for="name" value="Branch Name" />
							<jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
						</div>
						<div class="mt-4">
							<jet-label for="additionalInformation" value="Additional Information (Location)" />
							<jet-input id="additionalInformation" type="text" class="mt-1 block w-full" v-model="form.address.additionalInformation" />
						</div>
						<div class="mt-4">
							<jet-label for="postalCode" value="Postal Code" />
							<jet-input id="postalCode" type="number" class="mt-1 block w-full" v-model="form.address.postalCode" />
						</div>
					</div>
					<div>
						<div>
							<jet-label for="country" value="Country" />
							<jet-input id="country" type="text" class="mt-1 block w-full" v-model="form.address.country" required />
						</div>
						<div class="mt-4">
							<jet-label for="governate" value="Governate/State" />
							<jet-input id="governate" type="text" class="mt-1 block w-full" v-model="form.address.governate" required />
						</div>
						<div class="mt-4">
							<jet-label for="regionCity" value="Region/City" />
							<jet-input id="regionCity" type="text" class="mt-1 block w-full" v-model="form.address.regionCity" required />
						</div>
						<div class="mt-4">
							<jet-label for="street" value="Street" />
							<jet-input id="street" type="text" class="mt-1 block w-full" v-model="form.address.street" required />
						</div>
						<div class="mt-4">
							<jet-label for="buildingNumber" value="Building Number" />
							<jet-input id="buildingNumber" type="number" class="mt-1 block w-full" v-model="form.address.buildingNumber" required />
						</div>
					</div>
				</div>
			</form>
		</template>
		<template #footer>
			<div class="flex items-center justify-end mt-4">
	    		<jet-secondary-button @click="CancelAddBranch()">
   					Cancel
        		</jet-secondary-button>

	        	<jet-button class="ml-2" @click="SaveNewBranch()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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

        props: [
            'branch',
        ],

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    issuer_id: '',
                    type: 'B',
					address: {
						branchId: '',
						country: '',
						governate: '',
						regionCity: '',
						street: '',
						buildingNumber: '',
						postalCode : '',
						additionalInformation: '',

					}
                }),
				addingNew: false,
            }
        },

        methods: {
			ShowDialog() {
				this.addingNew = true;
			},
			CancelAddBranch() {
				this.addingNew = false;
			},
			SaveNewBranch() {
				this.addingNew = false;
			},
            submit() {
                this.form.post(this.route('register'), {
                    onFinish: () => this.form.reset('password', 'password_confirmation'),
                })
            }
        },
    }
</script>

