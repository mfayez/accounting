<template>
    <jet-dialog-modal :show="showDialog" max-width="lg" @close="showDialog = false">
		<template #title>
        	{{__('User Information')}}
        </template>

        <template #content>
			<jet-validation-errors class="mb-4" />

			<form @submit.prevent="submit">
				<div class="grid grid-cols-2 gap-4">
					<div>
						<jet-label for="name" :value="__('Name')" />
						<jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
					</div>
					<div>
						<jet-label for="Email" :value="__('Email')" />
						<jet-input id="Email" type="Email" class="mt-1 block w-full" v-model="form.email" required />
					</div>
					<div>
						<jet-label for="password" :value="__('Password')" />
						<jet-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />
					</div>
					<div>
						<jet-label :value="__('Role')" />
						<select id="type" v-model="form.current_team_id" class="mt-1 block w-full">
						  <option value="1">{{__('Administrator')}}</option>
						  <option value="2">{{__('Reviewer')}}</option>
						  <option value="3">{{__('Data Entry')}}</option>
						  <option value="4">{{__('ETA')}}</option>
						  <option value="5">{{__('Viewer')}}</option>
						</select>
					</div>
					<div class="col-span-2">
						<jet-label :value="__('Branches')" />
						<multiselect v-model="issuers" :options="branches" label="name" :placeholder="__('Select item')" :multiple="true"/>
					</div>
					<div class="col-span-2">
						<jet-label :value="__('Receivers and Vendors')" />
						<multiselect v-model="receivers" :options="customers" label="name" :placeholder="__('Select item')" :multiple="true"/>
					</div>
				</div>
			</form>
		</template>
		<template #footer>
			<div class="flex items-center justify-end mt-4">
	    		<jet-secondary-button @click="CancelAddUser()">
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
            pUser: {
				Type: Object,
				default:null
			},
        },

        data() {
            return {
				branches: [],
				customers: [],
				issuers: [],
				receivers: [],
				errors: [],
                form: this.$inertia.form({
                    name: '',
                    email: '',
                    password: '',
					issuers: [],
					receivers: [],
                }),
				showDialog: false,
            }
        },

        methods: {
			ShowDialog() {
				this.issuers = [];
				this.receivers = [];
				if (this.pUser !== null){
					this.form.name = this.pUser.name;
					this.form.email = this.pUser.email;
					this.form.current_team_id = this.pUser.current_team_id;
					for(var i = 0; i < this.pUser.issuers.length; i++)
						this.issuers.push(this.branches.find(option => option.Id === this.pUser.issuers[i].Id));
					for(var i = 0; i < this.pUser.receivers.length; i++)
						this.receivers.push(this.customers.find(option => option.Id === this.pUser.receivers[i].Id));
				}
				this.showDialog = true;
			},
			CancelAddUser() {
				this.showDialog = false;
			},
			SaveUser() {
				this.form.issuers = [];
				this.form.receivers = [];
				for(var i = 0; i < this.issuers.length; i++)
					this.form.issuers.push(this.issuers[i].Id);
				for(var i = 0; i < this.receivers.length; i++)
					this.form.receivers.push(this.receivers[i].Id);
                axios.put(route('users.update', {'user': this.pUser.id}), this.form)
				.then(response => {
					location.reload();
                }).catch(error => {
                    this.form.processing = false;
					this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;//.password[0];
                    //this.$refs.password.focus()
                });
			},
			SaveNewUser() {
				this.form.issuers = [];
				this.form.receivers = [];
				for(var i = 0; i < this.issuers.length; i++)
					this.form.issuers.push(this.issuers[i].Id);
				for(var i = 0; i < this.receivers.length; i++)
					this.form.receivers.push(this.receivers[i].Id);
					
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
				if (this.pUser == null)
					this.SaveNewUser();
				else
					this.SaveUser();
            }
        },
		created: function created() {
			axios.get(route('json.branches'))
			.then(response => {
				this.branches = response.data;
				if (this.pUser)
					for(var i = 0; i < this.pUser.issuers.length; i++)
						this.issuers.push(this.branches.find(option => option.Id === this.pUser.issuers[i].Id));
            }).catch(error => {

            });
			axios.get(route('json.customers'))
			.then(response => {
				this.customers = response.data;
				if (this.pUser)
					for(var i = 0; i < this.pUser.receivers.length; i++)
						this.receivers.push(this.customers.find(option => option.Id === this.pUser.receivers[i].Id));
            }).catch(error => {

            });
		},



    }
</script>

