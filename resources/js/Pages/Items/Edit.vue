<template>
    <jet-dialog-modal :show="addingNew" @close="addingNew = false">
		<template #title>
        	Add new item
        </template>

        <template #content>
			<jet-validation-errors class="mb-4" />

			<form @submit.prevent="submit">
				<div class="grid grid-cols-2 gap-4">
					<div>
							<jet-label for="codeType" value="Select code type" />
							<select id="codeType" v-model="form.codeType" 
										class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
								<option value="EGS">EGS</option>
								<option value="GS1">GS1</option>
							</select>
							<jet-label for="parentCode" value="Parent Code" />
							<jet-input id="parentCode" type="number" class="mt-1 block w-full" v-model="form.parentCode" required autofocus />
							
							<jet-label for="itemCode" value="Item Code" />
							<jet-input id="itemCode" type="text" class="mt-1 block w-full" v-model="form.itemCode" required autofocus />

							<jet-label for="codeName" value="Item label" />
							<jet-input id="codeName" type="text" class="mt-1 block w-full" v-model="form.codeName" required autofocus />
							
							<jet-label for="codeNameAr" value="Item Label (Arabic)" />
							<jet-input id="codeNameAr" type="text" class="mt-1 block w-full" v-model="form.codeNameAr" required autofocus />
					</div>
					<div>

							<jet-label for="activeFrom" value="Activation Start Date" />
							<jet-input id="activeFrom" type="text" class="mt-1 block w-full" v-model="form.activeFrom" required autofocus />

							<jet-label for="activeTo" value="Activation Expire Date" />
							<jet-input id="activeTo" type="text" class="mt-1 block w-full" v-model="form.activeTo" required autofocus />
							
							<jet-label for="description" value="Item Description" />
							<jet-input id="description" type="text" class="mt-1 block w-full" v-model="form.description" required autofocus />
							
							<jet-label for="descriptionAr" value="Item Description (Arabic)" />
							<jet-input id="descriptionAr" type="text" class="mt-1 block w-full" v-model="form.descriptionAr" required autofocus />
							
							<jet-label for="requestReason" value="Reason for adding the item" />
							<jet-input id="requestReason" type="text" class="mt-1 block w-full" v-model="form.requestReason" required autofocus />
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
				errors: [],
                form: this.$inertia.form({
					codeType: 'EGS',
					parentCode: "",
					itemCode: "",
					codeName: "",
					codeNameAr: "",
					activeFrom: "",
					activeTo: "",
					description: "",
					descriptionAr: "",
					requestReason: ""
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
                axios.post(route('eta.items.store'), this.form)
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
            submit() {
				SaveNewBranch();
            }
        },
    }
</script>
