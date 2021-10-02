<template>
    <input :type="type" class="border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm" 
		:value="modelValue" 
		@input="$emit('update:modelValue', $event.target.value)" 
		ref="input" 
		v-bind:class="[isRounded ? 'rounded-md' : '']"
	>
</template>

<script>
    export default {
        props: {
			isRounded: {
				type: Boolean,
				default: true,
			},
            type: {
                type: String,
                default: 'submit',
            },
			modelValue: {
				type: Object
			}
        },

        emits: ['update:modelValue'],

        methods: {
            focus() {
                this.$refs.input.focus()
            }
        }
    }
</script>

