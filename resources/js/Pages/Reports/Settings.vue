<template>
    <jet-dialog-modal :show="showDialog" max-width="lg" @close="showDialog = false">
		<template #title>
        	{{__('Invoice PDF Settings')}}
        </template>

        <template #content>
			<jet-validation-errors class="mb-4" />

			<form @submit.prevent="submit">
				<div class="grid grid-cols-2 gap-4">
					<div class="col-span-2">
						<jet-label for="footer" :value="__('Footer')" />
						<jet-input id="footer" type="text" class="mt-1 block w-full" v-model="form.footer" required autofocus />
					</div>
					<div>
                        <jet-checkbox name="showQR" id="terms" v-model:checked="form.showQR" />
						<!--<jet-input id="QR" type="checkbox" class="" v-model="form.showQR" required autofocus />-->
						<jet-label for="QR" :value="__('Show QR Code')" class="ms-2"/>
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
	import Multiselect from '@suadelabs/vue3-multiselect'

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
				settings: [],
				errors: [],
                form: this.$inertia.form({
					type: 'invoice settings',
                    footer: '',
					showQR: true,
                }),
				showDialog: false,
            }
        },

        methods: {
			ShowDialog() {
				this.showDialog = true;
				axios.get(route('settings.json'), { params: { type: this.form.type} })
				.then(response => {
					this.settings = response.data;
					this.form.footer = this.settings.footer;
					this.form.showQR = this.settings.showQR == '1' ? true : false;
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

