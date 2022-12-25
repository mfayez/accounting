<template>
    <div class="px-3 py-6 w-screen h-screen flex justify-center items-center">
        <div
            class="flex w-full mx-auto overflow-hidden bg-white rounded-lg shadow-xl lg:w-1/3"
        >
            <div class="w-full px-6 py-8 md:px-8">
                <h2 class="text-2xl font-semibold text-center text-gray-700">
                    Accounting Master
                </h2>

                <p class="text-xl text-center text-gray-600">Welcome back!</p>

			<form @submit.prevent="submit">
                <div class="mt-4">
                    <label
                        class="block mb-2 text-sm font-medium text-gray-600"
                        for="LoggingEmailAddress"
                        >Email Address</label
                    >
                    <input
                        id="LoggingEmailAddress"
                        class="block w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:outline-none focus:border-black"
                        type="email"
                        v-model="form.email"
                    />
                    <input-error :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <div class="flex justify-between">
                        <label
                            class="block mb-2 text-sm font-medium text-gray-600"
                            for="loggingPassword"
                            >Password</label
                        >
                        <a
                            href="#"
                            class="text-xs text-gray-500 hover:underline"
                            >Forget Password?</a
                        >
                    </div>

                    <input
                        id="loggingPassword"
                        class="block w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:outline-none focus:border-black"
                        type="password"
                        v-model="form.password"
                    />
                    <input-error :message="form.errors.password" />
                </div>

                <div class="mt-8">
                    <button
                        class="w-full px-4 py-2 tracking-wide text-white transition-colors duration-200 transform bg-gray-700 rounded hover:bg-gray-600 focus:outline-none focus:bg-gray-600"
                        :disabled="formIsProcessing"
                        @click="submit"
                    >
                        {{ formIsProcessing ? "Loading !" : "Login" }}
                    </button>
                </div>
			</form>
            </div>
        </div>
    </div>
</template>

<script>
import InputError from "@/Jetstream/InputError";
export default {
    components: {
        InputError,
    },
    props: {},
    data() {
        return {
            form: this.$inertia.form({
                email: "",
                password: "",
                remember: false,
            }),
        };
    },
    methods: {
        submit() {
            this.form.clearErrors();

            this.form
                .transform((data) => ({
                    ...data,
                    remember: this.form.remember ? "on" : "",
                }))
                .post(this.route("login"), {
                    onFinish: () => this.form.reset("password"),
                });
        },
    },
    computed: {
        formIsProcessing() {
            return this.form.processing;
        },
    },
};
</script>
<style scoped>
button:disabled {
    cursor: not-allowed;
}
input:focus {
    box-shadow: none;
}
</style>
