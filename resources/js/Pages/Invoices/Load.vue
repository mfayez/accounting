<template>
    <jet-dialog-modal :show="addingNew" @close="addingNew = false">
		<template #title>
        	Loading invoices from ETA
        </template>

        <template #content>
			<jet-validation-errors class="mb-4" />
			<div>
				<label for="sync1">Synchronizing Issued Invoices...</label><br/>
				<progress class="w-full" id="sync1" :value="progress1.value" :max="progress1.maxValue"> 
					{{progress1.value}}% 
				</progress>
				<label for="sync2">Synchronizing Received Invoices...</label><br/>
				<progress class="w-full" id="sync2" :value="progress2.value" :max="progress2.maxValue"> 
					{{progress2.value}}% 
				</progress>
			</div>
		</template>
		<template #footer>
			<div class="flex items-center justify-end mt-4">
	    		<jet-secondary-button @click="CancelAdd()">
   					Cancel
        		</jet-secondary-button>
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
				progress1: {
					value: 0,
					maxValue: 100
				},
				progress2: {
					value: 0,
					maxValue: 100
				},
				form: {
					value: 0,
				},
				addingNew: false,
            }
        },

        methods: {
			ShowDialog() {
				this.addingNew = true;
                this.$nextTick(() => this.LoadETA1());
			},
			CancelAdd() {
				this.addingNew = false;
			},
			LoadETA1() {
				this.form.value = this.progress1.value + 1;
                axios.post(route('eta.invoices.sync.issued'), this.form)
				.then(response => {
					this.progress1.maxValue = response.data.totalPages;
					this.progress1.value  = this.progress1.value + 1;
					if (this.progress1.value < this.progress1.maxValue)
						this.$nextTick(() => this.LoadETA1());
					else
						this.$nextTick(() => this.LoadETA2());
                }).catch(error => {
					this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;
                });
			},
			LoadETA2() {
				this.form.value = this.progress2.value + 1;
                axios.post(route('eta.invoices.sync.received'), this.form)
				.then(response => {
					this.progress2.maxValue = response.data.totalPages;
					this.progress2.value  = this.progress2.value + 1;
					if (this.progress2.value < this.progress2.maxValue)
						this.$nextTick(() => this.LoadETA2());
					else
						this.CancelAdd();
                }).catch(error => {
					this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;
                });
			},
            submit() {
            }
        },
    }
</script>

