<template>
    <jet-dialog-modal :show="showDialog" @close="showDialog = false">
		<template #title>
        	{{__('Customer Information')}}
        </template>

        <template #content>
			<jet-validation-errors class="mb-4" />

			<form @submit.prevent="submit">
				<div class="grid grid-cols-2 gap-4">
						<div>
							<jet-label for="type"  :value="__('Customer Type')" />
							<select id="type" v-model="form.type" class="mt-1 block w-full">
							  <option value="P">{{__('Person')}}</option>
							  <option value="B">{{__('Business')}}</option>
							</select>
						</div>
						<div> 
							<jet-label for="id" :value="__('Tax Registration Number')" />
							<jet-input id="id" type="text" class="mt-1 block w-full" v-model="form.receiver_id" required />
						</div>
						<div> 
							<jet-label for="name" :value="__('Name')" />
							<jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
						</div>
						<div> 
							<jet-label for="code" :value="__('Internal Code')" />
							<jet-input id="code" type="text" class="mt-1 block w-full" v-model="form.code" required />
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
	import Multiselect from '@suadelabs/vue3-multiselect'

    export default {
        components: {
			Multiselect,
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
            customer:{
				Type: Object,
				default:null
			},
        },

        data() {
            return {
				errors: [],
                form: this.$inertia.form({
                    name: '',
                    receiver_id: '',
                    type: 'B',
					code: ''
				}),
				showDialog: false,
            }
        },

        methods: {
			ShowDialog() {
				if (this.customer !== null){
					this.form.name = this.customer.name;
					this.form.type = this.customer.type;
					this.form.receiver_id = this.customer.receiver_id;
					this.form.code = this.customer.code;
				}
				this.showDialog = true;
			},
			CancelAddBranch() {
				this.showDialog = false;
			},
			SaveBranch() {
                axios.put(route('customers.update', {'customer': this.customer.Id}), this.form)
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
                axios.post(route('customers.store'), this.form)
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
				if (this.customer == null)
					this.SaveNewBranch();
				else
					this.SaveBranch();
            }
        },
    }
</script>

