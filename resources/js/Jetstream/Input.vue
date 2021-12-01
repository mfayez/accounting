<template>
    <input :type="type" class="border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm" 
		:value="modelValue" :id="inputId"
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
            inputId: {
                type: String,
                default: 'input1',
            },
            type: {
                type: String,
                default: 'submit',
            },
			modelValue: {
				type: String
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

