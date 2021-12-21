<template>
    <jet-dialog-modal :show="showDialog" max-width="sm" @close="showDialog = false">
		<template #title>
        	{{__('User Information')}}
        </template>

        <template #content>
			<jet-validation-errors class="mb-4" />

			<form @submit.prevent="submit">
				<div class="grid grid-cols-1 gap-4">
					<div>
						<div>
							<jet-label for="name" :value="__('Name')" />
							<jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
						</div>
						<div class="mt-4">
							<jet-label for="Email" :value="__('Email')" />
							<jet-input id="Email" type="Email" class="mt-1 block w-full" v-model="form.email" required />
						</div>
						<div class="mt-4">
							<jet-label for="password" :value="__('Password')" />
							<jet-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />
						</div>
						<div class="mt-4">
							<jet-label :value="__('Role')" />
							<select id="type" v-model="form.current_team_id" class="mt-1 block w-full">
							  <option value="1">{{__('Administrator')}}</option>
							  <option value="2">{{__('Reviewer')}}</option>
							  <option value="3">{{__('Data Entry')}}</option>
							  <option value="4">{{__('ETA')}}</option>
							  <option value="5">{{__('Viewer')}}</option>
							</select>
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
	import Multiselect from '@suadelabs/vue3-multiselect'

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
			Multiselect,
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
                    email: '',
                    password: '',
                }),
				showDialog: false,
            }
        },

        methods: {
			ShowDialog() {
				if (this.branch !== null){
					this.form.name = this.branch.name;
					this.form.email = this.branch.email;
					this.form.current_team_id = this.branch.current_team_id;
				}
				this.showDialog = true;
			},
			CancelAddBranch() {
				this.showDialog = false;
			},
			SaveBranch() {
                axios.put(route('users.update', {'user': this.branch.id}), this.form)
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
                axios.post(route('users.store'), this.form)
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
