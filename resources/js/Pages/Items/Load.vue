<template>
    <jet-dialog-modal :show="addingNew" @close="addingNew = false">
		<template #title>
        	{{__('Loading items from ETA')}}
        </template>

        <template #content>
			<jet-validation-errors class="mb-4" />
			<div>
				<label for="sync">{{__('Synchronizing')}} {{form.type}}</label><br/>
				<progress class="w-full" id="sync" :value="progress.value" :max="progress.maxValue"> 
					{{progress.value}}% 
				</progress>
			</div>
		</template>
		<template #footer>
			<div class="flex items-center justify-end mt-4">
	    		<jet-secondary-button @click="CancelAdd()">
   					{{__('Cancel')}}
        		</jet-secondary-button>
			</div>
	   </template>
	</jet-dialog-modal>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetActionSection from '@/Jetstream/ActionSection.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetCheckbox from '@/Jetstream/Checkbox.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetSectionBorder from '@/Jetstream/SectionBorder.vue'
    import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'



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
				progress: {
					value: 0,
					maxValue: 100
				},
				form: {
					value: 0,
                    type: "EGS"
				},
				addingNew: false,
            }
        },

        methods: {
			ShowDialog() {
				this.addingNew = true;
                this.$nextTick(() => this.LoadETA());
			},
			CancelAdd() {
				this.addingNew = false;
			},
			LoadETA() {
				this.form.value = this.progress.value + 1;
                axios.post(route('eta.items.sync'), this.form)
				.then(response => {
					this.progress.maxValue = response.data.totalPages;
					this.progress.value  = this.progress.value + 1;
					if (this.progress.value < this.progress.maxValue)
						this.$nextTick(() => this.LoadETA());
                    else if (this.form.type == "EGS")
                    {
                        this.progress.maxValue = 0;
                        this.progress.value = 0;
                        this.form.type = "GS1";
                        this.$nextTick(() => this.LoadETA());
                    }
					else
                    {
                        this.progress.maxValue = 0;
                        this.progress.value = 0;
                        this.form.type = "Requests";
						this.LoadETARequests();
                    }
                }).catch(error => {
					this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;
                });
			},
            LoadETARequests() {
				this.form.value = this.progress.value + 1;
                axios.post(route('eta.items.requests.sync'), this.form)
				.then(response => {
					this.progress.maxValue = response.data.totalPages;
					this.progress.value  = this.progress.value + 1;
					if (this.progress.value < this.progress.maxValue)
						this.$nextTick(() => this.LoadETARequests());
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

