<template>
    <app-layout>
        <template #header>
			<h1>Create new item</h1>
        </template>
            <div class="bg-white px-12 pb-8 overflow-hidden shadow-xl sm:rounded-lg">
				<jet-validation-errors class="mb-4" />

				<form @submit.prevent="submit">
					<div>
						<jet-label for="name" value="Name" />
						<jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
					</div>

					<div class="mt-4">
						<jet-label for="ETACode" value="ETA Code" />
						<jet-input id="ETACode" type="text" class="mt-1 block w-full" v-model="form.eta_code" required autocomplete="eta-code"/>
					</div>

					<div class="mt-4">
						<jet-label for="unit_price" value="Unit Price" />
						<jet-input id="unit_price" type="password" class="mt-1 block w-full" v-model="form.unit_price" required autocomplete="new-password" />
					</div>

					<div class="flex items-center justify-end mt-4">
						<jet-button class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
							Save
						</jet-button>
					</div>
				</form>
            </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetAuthenticationCard from '@/Jetstream/AuthenticationCard'
    import JetButton from '@/Jetstream/Button'
    import JetInput from '@/Jetstream/Input'
    import JetCheckbox from "@/Jetstream/Checkbox";
    import JetLabel from '@/Jetstream/Label'
    import JetValidationErrors from '@/Jetstream/ValidationErrors'

    export default {
        components: {
            AppLayout,
            JetAuthenticationCard,
            JetButton,
            JetInput,
            JetCheckbox,
            JetLabel,
            JetValidationErrors
        },

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    eta_code: '',
                    unit_price: '',
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('items.store'), {
                    onFinish: () => this.form.reset('name, etapassword', 'password_confirmation'),
                })
            }
        }
    }
</script>
